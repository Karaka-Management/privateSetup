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

use Modules\ClientManagement\Models\AttributeValueType;
use Modules\Profile\Models\ContactType;
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Stdlib\Base\AddressType;
use phpOMS\System\MimeType;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Email;
use phpOMS\Utils\RnG\Phone;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Setup accounts
 *
 * @var \Modules\ClientManagement\Controller\ApiController $module
 */
//region Accounts
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('ClientManagement');
TestUtils::setMember($module, 'app', $app);

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

$LOREM = \array_slice(Text::LOREM_IPSUM, 0, 50);

$LOREM_COUNT = \count($LOREM) - 1;
$CUSTOMERS   = 100;
$numbers     = [];

// client attribute types (e.g. color, material etc.)
foreach ($LOREM as $word) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = \mt_rand(2, 5);

    $request->setData('name', '_' . $word); // identifier of the attribute
    $request->setData('language', ISO639x1Enum::_EN);
    $request->setData('title', 'EN:' . $word);

    $module->apiClientAttributeTypeCreate($request, $response);

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

        $module->apiClientAttributeTypeL11nCreate($request, $response);
    }

    $type = AttributeValueType::getRandom();

    // create default values
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

        $module->apiClientAttributeValueCreate($request, $response);

        if ($type === AttributeValueType::_STRING) {
            foreach ($variables['languages'] as $language) {
                if ($language === ISO639x1Enum::_EN) {
                    continue;
                }

                $response = new HttpResponse();

                $request->setData('language', $language, true);
                $request->setData('country', ISO3166TwoEnum::_USA, true);
                $request->setData('value', \strtoupper($language) . ':' . $LOREM[\mt_rand(0, $LOREM_COUNT)], true);

                $module->apiClientAttributeValueCreate($request, $response);
            }
        }
    }
}

echo '░';

$count = $CUSTOMERS;
$interval = (int) \ceil($count / 9);
$z = 0;
$p = 0;

for ($i = 0; $i < $CUSTOMERS; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 2;

    do {
        $number = \mt_rand(100000, 599999);
    } while (\in_array($number, $numbers));
    $numbers[] = $number;

    $request->setData('number', (string) $number);
    $request->setData('name1', \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT - 1)]));
    $request->setData('name2', \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT - 1)]));

    if (\mt_rand(1, 100) < 26) {
        $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_1-1');
        $request->setData('info', \preg_replace('/^.+\n/', '', $MARKDOWN));
    }

    $request->setData('type', AddressType::getRandom());
    $request->setData('address',
        \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)])
        . ' ' . \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)])
        . ' ' . \mt_rand(1, 1000)
    );
    $request->setData('postal', \str_pad((string) \mt_rand(1000, 99999), 5, '0', \STR_PAD_LEFT));
    $request->setData('city', \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)]));
    $request->setData('country', ISO3166TwoEnum::getRandom());
    $request->setData('state', '');

    $module->apiClientCreate($request, $response);

    $cId = $response->get('')['response']->getId();

    //region attributes
    for ($j = 1; $j < $LOREM_COUNT; ++$j) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);

        $request->setData('client', $cId);
        $request->setData('type', $j);
        $request->setData('value', \mt_rand(($j - 1) * $LOREM_COUNT + 1, $j * $LOREM_COUNT));

        $module->apiClientAttributeCreate($request, $response);
    }
    //endregion

    //region contacts
    $count = \mt_rand(0, 4);
    for ($j = 0; $j < $count; ++$j) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 2;
        $request->setData('client', $cId);
        $request->setData('type', $type = ContactType::getRandom());
        $request->setData('subtype', 0);

        if ($type === ContactType::PHONE) {
            $request->setData('content', Phone::generatePhone());
        } elseif ($type === ContactType::EMAIL) {
            $request->setData('content', Email::generateEmail());
        } elseif ($type === ContactType::FAX) {
            $request->setData('content', Phone::generatePhone());
        } elseif ($type === ContactType::WEBSITE) {
            $request->setData('content', 'https://' . Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)] . '.com');
        }

        $module->apiContactElementCreate($request, $response);
    }
    //endregion

    //region profile image
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $image                 = \imagecreate(256, 256);
    $image_backgroundColor = \imagecolorallocate($image, 54, 151, 219);
    $image_textColor       = \imagecolorallocate($image, 52, 58, 64);

    \imagefill($image, 0, 0, $image_backgroundColor);
    \imagettftext(
        $image, 35, 0, 128 - 83, 128 + 15,
        $image_textColor,
        __DIR__ . '/files/SpaceMono-Bold.ttf',
        (string) $number
    );
    \imagepng($image, __DIR__ . '/temp/' . $number . '.png');
    \imagedestroy($image);

    $request->header->account = \mt_rand(2, 5);
    $request->setData('name', $number . ' backend');
    $request->setData('client', $cId);
    $request->setData('type', 'backend_image');

    TestUtils::setMember($request, 'files', [
        'file1' => [
            'name'     => $number . '_backend.png',
            'type'     => MimeType::M_PNG,
            'tmp_name' => __DIR__ . '/temp/' . $number . '.png',
            'error'    => \UPLOAD_ERR_OK,
            'size'     => \filesize(__DIR__ . '/temp/' . $number . '.png'),
        ],
    ]);

    $module->apiFileCreate($request, $response);
    //endregion

    //region client files
    $files = \scandir(__DIR__ . '/media/types');

    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === 'Video.mp4' || \mt_rand(1, 100) < 76) {
            continue;
        }

        \copy(__DIR__ . '/media/types/' . $file, __DIR__ . '/temp/' . $file);

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);
        $request->setData('client', $cId);

        TestUtils::setMember($request, 'files', [
            'file1' => [
                'name'     => $file,
                'type'     => \explode('.', $file)[1],
                'tmp_name' => __DIR__ . '/temp/' . $file,
                'error'    => \UPLOAD_ERR_OK,
                'size'     => \filesize(__DIR__ . '/temp/' . $file),
            ],
        ]);

        $module->apiFileCreate($request, $response);
    }
    //endregion

    //region note
    for ($k = 0; $k < 20; ++$k) {
        if (\mt_rand(1, 100) < 76) {
            continue;
        }

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);

        $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');

        $request->setData('id', $cId);
        $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
        $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));

        $module->apiNoteCreate($request, $response);
    }
    //endregion

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo \str_repeat('░', 9 - $p);
