<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   OrangeManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

use Modules\Tasks\Models\TaskPriority;
use Modules\Tasks\Models\TaskStatus;
use Modules\Admin\Models\AccountMapper;
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Localization\ISO639x1Enum;
use Modules\Support\Models\AttributeValueType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Create support
 *
 * @var \Modules\Support\Controller\ApiController $module
 */
//region Support
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Support');
TestUtils::setMember($module, 'app', $app);

$TICKET_COUNT = 250;
$LOREM_LONG_COUNT  = \count(Text::LOREM_IPSUM) - 1;
$LANGUAGES    = \count($variables['languages']);
$ACCOUNTS     = AccountMapper::count();
$LOREM = \array_slice(Text::LOREM_IPSUM, 0, 5);
$LOREM_COUNT  = \count($LOREM) - 1;

// Create apps (besides the default app)
$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = \mt_rand(2, 5);
$request->setData('name', Text::LOREM_IPSUM[\mt_rand(0, $LOREM_LONG_COUNT - 1)]);

$module->apiSupportAppCreate($request, $response);

echo '░';

$count = \count($LOREM);
$interval = (int) \ceil($count / 2);
$z = 0;
$p = 0;

// ticket attribute types (e.g. product.)
foreach ($LOREM as $word) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = \mt_rand(2, 5);

    $request->setData('name', '_' . $word); // identifier of the attribute
    $request->setData('language', ISO639x1Enum::_EN);
    $request->setData('title', 'EN:' . $word);

    $module->apiTicketAttributeTypeCreate($request, $response);

    $attrTypeId = $response->get('')['response']->getId();
    foreach ($variables['languages'] as $language) {
        if ($language === ISO639x1Enum::_EN) {
            continue;
        }

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);

        $request->setData('type', $attrTypeId);
        $request->setData('language', $language);
        $request->setData('title', \strtoupper($language) . ':' . $LOREM[\mt_rand(0, $LOREM_COUNT)]);

        $module->apiTicketAttributeTypeL11nCreate($request, $response);
    }

    $type = AttributeValueType::getRandom();

    // create default values IFF it should have a default value
    if (\mt_rand(1, 100) < 30) {
        foreach ($LOREM as $word) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = \mt_rand(2, 5);

            $request->setData('attributetype', $attrTypeId);
            $request->setData('type', $type);
            $request->setData('default', true);

            $value = null;
            if ($type === AttributeValueType::_INT) {
                $value = \mt_rand(-2147483647, 2147483647);
            } elseif ($type === AttributeValueType::_STRING) {
                $request->setData('language', ISO639x1Enum::_EN);
                $request->setData('country', ISO3166TwoEnum::_USA);
                $value = 'EN:' . $word;
            } elseif ($type === AttributeValueType::_FLOAT) {
                $value = \mt_rand(\PHP_INT_MIN, \PHP_INT_MAX) / \mt_rand(\PHP_INT_MIN, \PHP_INT_MAX);
            } elseif ($type === AttributeValueType::_DATETIME) {
                $value = (new \DateTime())->setTimestamp(\mt_rand(0, \PHP_INT_SIZE === 4 ? \PHP_INT_MAX : \PHP_INT_MAX >> 32))->format('Y-m-d H:i:s');
            }

            $request->setData('value', $value);

            $module->apiTicketAttributeValueCreate($request, $response);

            if ($type === AttributeValueType::_STRING) {
                foreach ($variables['languages'] as $language) {
                    if ($language === ISO639x1Enum::_EN) {
                        continue;
                    }

                    $response = new HttpResponse();

                    $request->setData('language', $language, true);
                    $request->setData('country', ISO3166TwoEnum::_USA, true);
                    $request->setData('value', \strtoupper($language) . ':' . $LOREM[\mt_rand(0, $LOREM_COUNT)], true);

                    $module->apiTicketAttributeValueCreate($request, $response);
                }
            }
        }
    }

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo \str_repeat('░', 2 - $p);

// Create tickets
$count = $TICKET_COUNT;
$interval = (int) \ceil($count / 7);
$z = 0;
$p = 0;

for ($i = 0; $i < $TICKET_COUNT; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');

    $request->header->account = \mt_rand(1, 5);
    $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
    $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));
    $request->setData('forward', \mt_rand(2, 5));
    $request->setData('for', \mt_rand(1, $ACCOUNTS));

    ($DUE_DATE = new \DateTime())->setTimestamp(\time() + \mt_rand(-100000000, 100000000));
    $request->setData('due', $DUE_DATE->format('Y-m-d H:i:s'));
    $request->setData('priority', TaskPriority::getRandom());

    // tags
    $tags      = [];
    $TAG_COUNT = \mt_rand(0, 4);
    $added     = [];

    for ($j = 0; $j < $TAG_COUNT; ++$j) {
        $tagId = \mt_rand(1, $LOREM_LONG_COUNT - 1);

        if (!\in_array($tagId, $added)) {
            $added[] = $tagId;
            $tags[]  = ['id' => $tagId];
        }
    }

    if (!empty($tags)) {
        $request->setData('tags', \json_encode($tags));
    }

    $module->apiTicketCreate($request, $response);
    $ticketId = $response->get('')['response']->getId();

    //region attributes
    for ($j = 1; $j < $LOREM_COUNT; ++$j) {
        // create custom value
        $type = AttributeValueType::getRandom();

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);

        $request->setData('type', $type);
        $request->setData('default', false);

        $value = null;
        if ($type === AttributeValueType::_INT) {
            $value = \mt_rand(-2147483647, 2147483647);
        } elseif ($type === AttributeValueType::_STRING) {
            $request->setData('language', ISO639x1Enum::_EN);
            $request->setData('country', ISO3166TwoEnum::_USA);
            $value = 'EN:' . $word;
        } elseif ($type === AttributeValueType::_FLOAT) {
            $value = \mt_rand(\PHP_INT_MIN, \PHP_INT_MAX) / \mt_rand(\PHP_INT_MIN, \PHP_INT_MAX);
        } elseif ($type === AttributeValueType::_DATETIME) {
            $value = (new \DateTime())->setTimestamp(\mt_rand(0, \PHP_INT_SIZE === 4 ? \PHP_INT_MAX : \PHP_INT_MAX >> 32))->format('Y-m-d H:i:s');
        }

        $request->setData('value', $value);

        $module->apiTicketAttributeValueCreate($request, $response);
        $valueId = $response->get('')['response']->getId();

        if ($type === AttributeValueType::_STRING) {
            foreach ($variables['languages'] as $language) {
                if ($language === ISO639x1Enum::_EN) {
                    continue;
                }

                $response = new HttpResponse();

                $request->setData('language', $language, true);
                $request->setData('country', ISO3166TwoEnum::_USA, true);
                $request->setData('value', \strtoupper($language) . ':' . $LOREM[\mt_rand(0, $LOREM_COUNT)], true);

                $module->apiTicketAttributeValueCreate($request, $response);
            }
        }

        // create attribute
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);

        $request->setData('ticket', $ticketId);
        $request->setData('type', $j);

        // @todo: sometimes use default value instead of custom value (depends on attribute type)

        $request->setData('value', $valueId);

        $module->apiTicketAttributeCreate($request, $response);
    }
    //endregion

    $completion = 0;

    //region answers
    $ANSWER_COUNT  = \mt_rand(0, 3);
    for ($j = 0; $j < $ANSWER_COUNT; ++$j) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);
        $request->setData('ticket', $ticketId);
        $request->setData('status', TaskStatus::getRandom());
        $request->setData('time', \mt_rand(1, 100) < 50 ? \mt_rand(1, 60) : 0);

        $content = \mt_rand(1, 100);
        if ($content <= 80) {
            $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_1-1');
            $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));

            //region files
            $files = \scandir(__DIR__ . '/media/types');

            $fileCounter = 0;
            $toUpload    = [];
            $mFiles      = [];
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || $file === 'Video.mp4' || \mt_rand(1, 100) < 91) {
                    continue;
                }

                ++$fileCounter;

                if ($fileCounter === 1) {
                    \copy(__DIR__ . '/media/types/' . $file, __DIR__ . '/temp/' . $file);

                    $toUpload['file' . $fileCounter] = [
                        'name'     => $file,
                        'type'     => \explode('.', $file)[1],
                        'tmp_name' => __DIR__ . '/temp/' . $file,
                        'error'    => \UPLOAD_ERR_OK,
                        'size'     => \filesize(__DIR__ . '/temp/' . $file),
                    ];
                } else {
                    $mFiles[] = $variables['mFiles'][\mt_rand(0, \count($variables['mFiles']) - 1)];
                }
            }

            if (!empty($toUpload)) {
                TestUtils::setMember($request, 'files', $toUpload);
            }

            if (!empty($mFiles)) {
                $request->setData('media', \json_encode(\array_unique($mFiles)));
            }
            //endregion
        }

        ($DUE_DATE = new \DateTime())->setTimestamp(\time() + \mt_rand(-100000000, 100000000));
        $request->setData('due', $DUE_DATE->format('Y-m-d H:i:s'));
        $request->setData('priority', TaskPriority::getRandom());

        if (\mt_rand(0, 100) < 21) {
            $request->setData('completion', $completion = \mt_rand($completion, 100));
        }

        // @todo handle to
        // @todo handle cc

        $module->apiTicketElementCreate($request, $response);
    }
    //endregion

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo \str_repeat('░', 7 - $p);
//endregion
