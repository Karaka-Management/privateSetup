<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Karaka
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use Modules\Profile\Models\ContactType;
use Modules\SupplierManagement\Models\AttributeValueType;
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
 */
//region Accounts
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\SupplierManagement\Controller\ApiController $module */
$module = $app->moduleManager->get('SupplierManagement');
TestUtils::setMember($module, 'app', $app);

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

$LOREM = \array_slice(Text::LOREM_IPSUM, 0, 50);

$LOREM_COUNT = \count($LOREM) - 1;
$SUPPLIERS   = 100;
$numbers     = [];

// supplier attribute types (e.g. color, material etc.)
foreach ($LOREM as $word) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = \mt_rand(2, 5);

    $request->setData('name', '_' . $word); // identifier of the attribute
    $request->setData('language', ISO639x1Enum::_EN);
    $request->setData('title', 'EN:' . $word);

    $module->apiSupplierAttributeTypeCreate($request, $response);
    ++$apiCalls;

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

        $module->apiSupplierAttributeTypeL11nCreate($request, $response);
        ++$apiCalls;
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

        $module->apiSupplierAttributeValueCreate($request, $response);
        ++$apiCalls;

        if ($type === AttributeValueType::_STRING) {
            foreach ($variables['languages'] as $language) {
                if ($language === ISO639x1Enum::_EN) {
                    continue;
                }

                $response = new HttpResponse();

                $request->setData('language', $language, true);
                $request->setData('country', ISO3166TwoEnum::_USA, true);
                $request->setData('value', \strtoupper($language) . ':' . $LOREM[\mt_rand(0, $LOREM_COUNT)], true);

                $module->apiSupplierAttributeValueCreate($request, $response);
                ++$apiCalls;
            }
        }
    }
}

echo '░';

$count    = $SUPPLIERS;
$interval = (int) \ceil($count / 9);
$z        = 0;
$p        = 0;

for ($i = 0; $i < $SUPPLIERS; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 2;

    do {
        $number = \mt_rand(600000, 999999);
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

    $module->apiSupplierCreate($request, $response);
    ++$apiCalls;

    $sId = $response->get('')['response']->getId();

    //region attributes
    for ($j = 1; $j < $LOREM_COUNT; ++$j) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);

        $request->setData('supplier', $sId);
        $request->setData('type', $j);
        $request->setData('value', \mt_rand(($j - 1) * $LOREM_COUNT + 1, $j * $LOREM_COUNT));

        $module->apiSupplierAttributeCreate($request, $response);
        ++$apiCalls;
    }
    //endregion

    //region contacts
    $count = \mt_rand(0, 4);
    for ($j = 0; $j < $count; ++$j) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 2;
        $request->setData('account', $sId);
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
        ++$apiCalls;
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
    $request->setData('supplier', $sId);
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
    ++$apiCalls;
    //endregion

    //region supplier files
    $files = \scandir(__DIR__ . '/media/types');

    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === 'Video.mp4' || \mt_rand(1, 100) < 76) {
            continue;
        }

        \copy(__DIR__ . '/media/types/' . $file, __DIR__ . '/temp/' . $file);

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);
        $request->setData('supplier', $sId);

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
        ++$apiCalls;
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

        $request->setData('id', $sId);
        $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
        $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));

        $module->apiNoteCreate($request, $response);
        ++$apiCalls;
    }
    //endregion

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo \str_repeat('░', 9 - $p);
