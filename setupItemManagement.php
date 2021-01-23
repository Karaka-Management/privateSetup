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

use Modules\ItemManagement\Models\AttributeValueType;
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\System\MimeType;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Setup accounts
 *
 * @var \Modules\ItemManagement\Controller\ApiController $module
 */
//region Accounts
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('ItemManagement');
TestUtils::setMember($module, 'app', $app);

$LOREM = \array_slice(Text::LOREM_IPSUM, 0, 50);

$LOREM_COUNT = \count($LOREM) - 1;
$ITEMS       = 100;
$numbers     = [];

// item attribute types (e.g. color, material etc.)
foreach ($LOREM as $word) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = \mt_rand(2, 5);

    $request->setData('name', '_' . $word); // identifier of the attribute
    $request->setData('language', ISO639x1Enum::_EN);
    $request->setData('title', 'EN:' . $word);

    $module->apiItemAttributeTypeCreate($request, $response);

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

        $module->apiItemAttributeTypeL11nCreate($request, $response);
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
            $value = \mt_rand(\PHP_INT_MIN, \PHP_INT_MAX);
        } elseif ($type === AttributeValueType::_STRING) {
            $request->setData('language', ISO639x1Enum::_EN);
            $request->setData('country', ISO3166TwoEnum::_USA);
            $value = 'EN:' . $word;
        } elseif ($type === AttributeValueType::_FLOAT) {
            $value = \mt_rand(\PHP_INT_MIN, \PHP_INT_MAX) / \mt_rand(\PHP_INT_MIN, \PHP_INT_MAX);
        } elseif ($type === AttributeValueType::_DATETIME) {
            $value = (new \DateTime())->setTimestamp(\mt_rand(0, \PHP_INT_SIZE == 4 ? \PHP_INT_MAX : \PHP_INT_MAX >> 32))->format('Y-m-d H:i:s');
        }

        $request->setData('value', $value);

        $module->apiItemAttributeValueCreate($request, $response);

        if ($type === AttributeValueType::_STRING) {
            foreach ($variables['languages'] as $language) {
                if ($language === ISO639x1Enum::_EN) {
                    continue;
                }

                $response = new HttpResponse();

                $request->setData('language', $language, true);
                $request->setData('country', ISO3166TwoEnum::_USA, true);
                $request->setData('value', \strtoupper($language) . ':' . $LOREM[\mt_rand(0, $LOREM_COUNT)], true);

                $module->apiItemAttributeValueCreate($request, $response);
            }
        }
    }
}

// item l11n types (e.g. article names)
$L11N_TYPES = 30;
$i          = $L11N_TYPES;
$LOREM2     = \array_merge(['name1', 'name2', 'info'], $LOREM);

foreach ($LOREM2 as $word) {
    if (--$i < 0) {
        break;
    }

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = \mt_rand(2, 5);
    $request->setData('title', $word);

    $module->apiItemL11nTypeCreate($request, $response);
}

// items
for ($i = 0; $i < $ITEMS; ++$i) {
    //region item basic
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = \mt_rand(2, 5);

    do {
        $number = \mt_rand(100000, 999999);
    } while (\in_array($number, $numbers));
    $numbers[] = $number;

    $request->setData('number', (string) $number);

    $module->apiItemCreate($request, $response);
    $itemId = $response->get('')['response']->getId();
    //endregion

    //region item l11n
    // @todo: shouldn't this be limited by LOREM2???? maybe not, attributes != l11n types
    for ($j = 0; $j < $L11N_TYPES; ++$j) {
        foreach ($variables['languages'] as $language) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = \mt_rand(2, 5);

            $request->setData('item', $itemId);
            $request->setData('type', $j + 1);
            $request->setData('description', \strtoupper($language) . ':' . $LOREM[\mt_rand(0, $LOREM_COUNT)]);
            $request->setData('language', $language, true);

            $module->apiItemL11nCreate($request, $response);
        }
    }
    //endregion

    //region attributes
    for ($j = 1; $j < $LOREM_COUNT; ++$j) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);

        $request->setData('item', $itemId);
        $request->setData('type', $j);
        $request->setData('value', \mt_rand(($j - 1) * $LOREM_COUNT + 1, $j * $LOREM_COUNT));

        $module->apiItemAttributeCreate($request, $response);
    }
    //endregion

    //region item image
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    if (!\is_dir(__DIR__ . '/temp')) {
        \mkdir(__DIR__ . '/temp');
    }

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
    $request->setData('item', $itemId);
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
}
