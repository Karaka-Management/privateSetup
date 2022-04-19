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
use Modules\Exchange\Models\ImporterAbstract;
use Modules\Media\Controller\ApiController;
use phpOMS\DataStorage\Database\Connection\ConnectionFactory;
use phpOMS\DataStorage\Database\DatabaseStatus;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\RequestAbstract;

/**
 * OMS import class
 *
 * @package Modules\Exchange\Models\Interfaces\OMS
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class Importer extends ImporterAbstract
{
    /**
     * Account
     *
     * @var int
     * @since 1.0.0
     */
    private int $account = 1;

    /**
     * Import all data in time span
     *
     * @param \DateTime $start Start time (inclusive)
     * @param \DateTime $end   End time (inclusive)
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function import(\DateTime $start, \DateTime $end) : void
    {
        $this->importLanguage(new HttpRequest());
    }

    /**
     * Import data from request
     *
     * @param RequestAbstract $request Request
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function importFromRequest(RequestAbstract $request) : array
    {
        $start = new \DateTime($request->getData('start') ?? 'now');
        $end   = new \DateTime($request->getData('end') ?? 'now');

        $lang             = [];
        $lang['Exchange'] = include __DIR__ . '/Lang/' . $request->getLanguage() . '.lang.php';

        $this->l11n->loadLanguage($request->header->l11n->getLanguage(), 'Exchange', $lang);

        if ($request->getData('db') !== null) {
            $this->remote = ConnectionFactory::create([
                'db'             => (string) ($request->getData('db')),
                'host'           => (string) ($request->getData('host') ?? ''),
                'port'           => (int) ($request->getData('port') ?? 0),
                'database'       => (string) ($request->getData('database') ?? ''),
                'login'          => (string) ($request->getData('login') ?? ''),
                'password'       => (string) ($request->getData('password') ?? ''),
                'datetimeformat' => (string) ($request->getData('datetimeformat') ?? 'Y-m-d H:i:s'),
            ]);

            $this->remote->connect();

            if ($this->remote->getStatus() !== DatabaseStatus::OK) {
                return ['status' => false];
            }
        }

        $this->account = $request->header->account;

        $result = ['status' => true];

        if ($request->getData('type') === 'language') {
            $this->importLanguage($request);
            $log            = new ExchangeLog();
            $log->createdBy = $this->account;
            $log->setType(ExchangeType::IMPORT);
            $log->message  = $this->l11n->getText($request->header->l11n->getLanguage(), 'Exchange', '', 'LangFileImported');
            $log->subtype  = 'language';
            $log->exchange = (int) $request->getData('id');

            $result['logs'][] = $log;
        }

        return $result;
    }

    /**
     * Import language
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function importLanguage(RequestAbstract $request) : void
    {
        $upload = ApiController::uploadFilesToDestination($request->getFiles());

        $fp = \fopen($upload['file0']['path'] . '/' . $upload['file0']['filename'], 'r');
        if ($fp === false) {
            return; // @codeCoverageIgnore
        }

        $header = \fgetcsv($fp, 0, ';', '"');

        if ($header === false) {
            return; // @codeCoverageIgnore
        }

        $languageArray      = [];
        $supportedLanguages = \array_slice($header, 4);
        $keyLengths         = [];

        while (($line = \fgetcsv($fp, 0, ';', '"')) !== false) {
            $translations = \array_slice($line, 4);

            $line[0] = \trim($line[0]);
            $line[1] = \trim($line[1]);
            $line[2] = \trim($line[2]);
            $line[3] = \trim($line[3]);

            if (($keyLengths[$line[0]][$line[1]][$line[2]] ?? 0) < \strlen($line[3])) {
                $keyLengths[$line[0]][$line[1]][$line[2]] = \strlen($line[3]);
            }

            foreach ($supportedLanguages as $index => $language) {
                if (empty(\trim($language))) {
                    continue; // @codeCoverageIgnore
                }

                $languageArray[$line[0]][$line[1]][$line[2]][$line[3]][\trim($language)] = $translations[$index];
            }
        }

        \fclose($fp);
        \unlink($upload['file0']['path'] . '/' . $upload['file0']['filename']);

        foreach ($languageArray as $module => $themes) {
            foreach ($themes as $theme => $files) {
                foreach ($files as $file => $keys) {
                    foreach ($supportedLanguages as $language) {
                        $langFile = __DIR__ . '/../../../../../../'
                            . $module . '/Theme/'
                            . $theme . '/Lang/'
                            . ($file === '' ? '' : $file . '.')
                            . \trim($language)
                            . '.lang.php';

                        if (\is_file($langFile)) {
                            \unlink($langFile);
                        }

                        $fp = \fopen($langFile, 'w+');
                        if ($fp === false) {
                            continue; // @codeCoverageIgnore
                        }

                        \fwrite($fp,
                            "<?php\n"
                            . "/**\n"
                            . " * Karaka\n"
                            . " *\n"
                            . " * PHP Version 8.1\n"
                            . " *\n"
                            . " * @package   Modules\Localization\n"
                            . " * @copyright Dennis Eichhorn\n"
                            . " * @license   OMS License 1.0\n"
                            . " * @version   1.0.0\n"
                            . " * @link      https://karaka.app\n"
                            . " */\n"
                            . "declare(strict_types=1);\n\n"
                            . "return ['" . ($file === '' ? $module : $file) . "' => [\n"
                        );

                        \ksort($keys);

                        foreach ($keys as $key => $values) {
                            $key = \ltrim($key, '*');

                            \fwrite($fp,
                                "    '" . $key . "'"
                                . \str_repeat(' ', $keyLengths[$module][$theme][$file] - \strlen($key))
                                . " => '"
                                . \str_replace(['\\', '\''], ['\\\\', '\\\''], $values[$language] ?? '')
                                . "',\n"
                            );
                        }

                        \fwrite($fp, "]];\n");
                        \fclose($fp);
                    }
                }
            }
        }
    }
}
