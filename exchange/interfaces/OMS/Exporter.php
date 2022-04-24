<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Interfaces
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Exchange\Interface;

use Modules\Exchange\Models\ExchangeLog;
use Modules\Exchange\Models\ExchangeType;
use Modules\Exchange\Models\ExporterAbstract;
use phpOMS\Message\RequestAbstract;
use phpOMS\Utils\StringUtils;

/**
 * OMS export class
 *
 * @package Modules\Exchange\Models\Interfaces\OMS
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class Exporter extends ExporterAbstract
{
    /**
     * Account
     *
     * @var int
     * @since 1.0.0
     */
    private int $account = 1;

    /**
     * Export all data in time span
     *
     * @param \DateTime $start Start time (inclusive)
     * @param \DateTime $end   End time (inclusive)
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function export(\DateTime $start, \DateTime $end) : array
    {
        return $this->exportLanguage();
    }

    /**
     * Export data from request
     *
     * @param RequestAbstract $request Request
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function exportFromRequest(RequestAbstract $request) : array
    {
        $start = new \DateTime($request->getData('start') ?? 'now');
        $end   = new \DateTime($request->getData('end') ?? 'now');

        $this->account = $request->header->account;

        $lang             = [];
        $lang['Exchange'] = include __DIR__ . '/Lang/' . $request->getLanguage() . '.lang.php';

        $this->l11n->loadLanguage($request->header->l11n->getLanguage(), 'Exchange', $lang);

        $result = [];

        if ($request->getData('type') === 'language') {
            $result = $this->exportLanguage();

            $log            = new ExchangeLog();
            $log->createdBy = $this->account;
            $log->setType(ExchangeType::EXPORT);
            $log->message  = $this->l11n->getText($request->header->l11n->getLanguage(), 'Exchange', '', 'LangFileExported');
            $log->subtype  = 'language';
            $log->exchange = (int) $request->getData('id');

            $result['logs'][] = $log;
        }

        return $result;
    }

    /**
     * Export language
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function exportLanguage() : array
    {
        $languageArray      = [];
        $supportedLanguages = [];

        $basePath = __DIR__ . '/../../../../../../';
        $modules  = \scandir($basePath);

        if ($modules === false) {
            return []; // @codeCoverageIgnore
        }

        foreach ($modules as $module) {
            $themePath = $basePath . $module . '/Theme/';

            if (!\is_dir($basePath . $module) || $module === '.' || $module === '..'
                || !\is_dir($themePath)
            ) {
                continue;
            }

            $module = \trim($module, '/');
            $themes = \scandir($themePath);

            if ($themes === false) {
                continue; // @codeCoverageIgnore
            }

            foreach ($themes as $theme) {
                $theme    = \trim($theme, '/');
                $langPath = $themePath . $theme . '/Lang/';

                if (!\is_dir($themePath . $theme) || $theme === '.' || $theme === '..'
                    || !\is_dir($langPath)
                ) {
                    continue;
                }

                $languages = \scandir($themePath . $theme . '/Lang/');
                if ($languages === false) {
                    continue; // @codeCoverageIgnore
                }

                foreach ($languages as $language) {
                    if (\stripos($language, '.lang.') === false) {
                        continue;
                    }

                    $components = \explode('.', $language);
                    $len        = \count($components);

                    if ($len === 3 || $len === 4) {
                        // normal language file
                        if ($len === 3) {
                            $supportedLanguages[] = $components[0];
                        } elseif ($len === 4) {
                            $supportedLanguages[] = $components[1];
                        }

                        $array = include $themePath . $theme . '/Lang/' . $language;
                        $array = \reset($array);

                        if ($array === false) {
                            continue; // @codeCoverageIgnore
                        }

                        if ($len === 3) {
                            foreach ($array as $key => $value) {
                                $languageArray[$module][$theme][''][$key][$components[0]] = $value;
                            }
                        } elseif ($len === 4) {
                            foreach ($array as $key => $value) {
                                $languageArray[$module][$theme][$components[0]][$key][$components[1]] = $value;
                            }
                        }
                    }
                }
            }

            // search for translations in tpl files which are not included in the language files
            $tplKeys = [];
            foreach ($themes as $theme) {
                if (!\is_dir($themePath . $theme) || $theme === '.' || $theme === '..') {
                    continue;
                }

                $theme = \trim($theme, '/');

                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($themePath . $theme . '/', \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST
                );

                /** @var \DirectoryIterator $iterator */
                foreach ($iterator as $item) {
                    if ($item->isDir() || !StringUtils::endsWith($item->getFilename(), '.tpl.php')) {
                        continue;
                    }

                    $template = \file_get_contents($item->getPathname());
                    $keys     = [];

                    if ($template === false) {
                        continue; // @codeCoverageIgnore
                    }

                    \preg_match_all('/(\$this\->getHtml\(\')([0-9a-zA-Z:\-]+)(\'\))/', $template, $keys, \PREG_PATTERN_ORDER);

                    foreach ($keys[2] ?? [] as $key) {
                        if (!isset($languageArray[$module][$theme][''][$key])) {
                            $tplKeys[$module][$theme][''][$key]['en']       = '';
                            $languageArray[$module][$theme][''][$key]['en'] = '';
                        }
                    }
                }
            }
        }

        $supportedLanguages = \array_unique($supportedLanguages);

        $content = '"Module";"Theme";"File";"ID";"' . \implode('";"', $supportedLanguages) . '"';
        foreach ($languageArray as $module => $themes) {
            foreach ($themes as $theme => $files) {
                foreach ($files as $file => $keys) {
                    foreach ($keys as $key => $value) {
                        $content .= "\n\"" . $module . '";"' . $theme . '";"' . $file . '";"';
                        $content .= ($file === '' && isset($tplKeys[$module][$theme][''][$key]) ? '*' : '') . $key . '"';

                        foreach ($supportedLanguages as $language) {
                            $content .= ';"' . ($value[$language] ?? '') . '"';
                        }
                    }
                }
            }
        }

        return [
            'type'    => 'file',
            'name'    => 'languages.csv',
            'content' => $content,
        ];
    }
}
