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
 * @link      https://jingga.app
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
 */
//region Accounts
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\ItemManagement\Controller\ApiController $module */
$module = $app->moduleManager->get('ItemManagement');
TestUtils::setMember($module, 'app', $app);

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

$items = \scandir(__DIR__ . '/itemmanagement/items');

function findAttributeIdByValue(array $defaultValues, mixed $value)
{
    foreach ($defaultValues as $val) {
        if ($val->valueStr === $val
            || $val->valueInt === $val
            || $val->valueDec === $val
        ) {
            return $val->getId();
        }
    }

    return 0;
}

$count    = \count($items);
$interval = (int) \ceil($count / 10);
$z        = 0;
$p        = 0;

$attributes   = \json_decode(\file_get_contents(__DIR__ . '/itemmanagement/attributes.json'));
$itemAttrType = [];

foreach ($attributes as $attribute) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 1;
    $request->setData('name', $attribute['name'] ?? '');
    $request->setData('title', $attribute['l11n'][0] ?? '');
    $request->setData('language', \array_keys($attribute['l11n'])[0] ?? 'en');
    $request->setData('is_required', $attribute['is_required'] ?? false);
    $request->setData('is_custom_allowed', $attribute['is_custom_allowed'] ?? false);
    $request->setData('validation_pattern', $attribute['validation_pattern'] ?? false);

    $module->apiItemAttributeTypeCreate($request, $response);

    $itemAttrType[$attribute['name']] = !\is_array($response->get('')['response'])
        ? $response->get('')['response']->toArray()
        : $response->get('')['response'];

    $isFirst = true;
    foreach ($attribute['l11n'] as $language => $l11n) {
        if ($isFirst) {
            $isFirst = false;
            continue;
        }

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('title', $l11n);
        $request->setData('language', $language);
        $request->setData('type', $itemAttrType[$attribute['name']]['id']);

        $module->apiItemAttributeTypeL11nCreate($request, $response);
    }
}

$itemAttrValue = [];
foreach ($attributes as $attribute) {
    $itemAttrValue[$attribute['name']] = [];

    foreach ($attribute['values'] as $value) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('value', $value['value'] ?? '');
        $request->setData('value_type', $attribute['value_type'] ?? 0);
        $request->setData('unit', $value['unit'] ?? '');
        $request->setData('default', isset($attribute['values']) && !empty($attribute['values']));
        $request->setData('attributetype', $itemAttrType[$attribute['name']]['id']);

        if (isset($value['l11n']) && !empty($value['l11n'])) {
            $request->setData('title', $value['l11n'][0] ?? '');
            $request->setData('language', \array_keys($value['l11n'])[0] ?? 'en');
        }

        $module->apiItemAttributeValueCreate($request, $response);

        $attrValue = !\is_array($response->get('')['response'])
            ? $response->get('')['response']->toArray()
            : $response->get('')['response'];

        $itemAttrValue[$attribute['name']][] = $attrValue;

        $isFirst = true;
        foreach (($value['l11n'] ?? []) as $language => $l11n) {
            if ($isFirst) {
                $isFirst = false;
                continue;
            }

            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = 1;
            $request->setData('title', $l11n);
            $request->setData('language', $language);
            $request->setData('value', $attrValue['id']);

            $module->apiItemAttributeValueL11nCreate($request, $response);
        }
    }
}

$attributeTypes  = ItemAttributeTypeMapper::getAll()->with('defaults')->execute();
$attributeValues = ItemAttributeValueMapper::getAll()->execute();
$l11nTypes       = ItemL11nTypeMapper::getAll()->execute();

// Change indexing for easier search later on.
foreach ($attributeTypes as $e) {
    $attributeTypes[$e->name] = $e;
}

foreach ($attributeValues as $e) {
    $attributeValues[$e->type][] = $e;
}

foreach ($l11nTypes as $e) {
    $l11nTypes[$e->title] = $e;
}

foreach ($items as $item) {
    if (!\is_dir(__DIR__ . '/itemmanagement/' . $item) || $item === '..' || $item === '.') {
        ++$z;
        if ($z % $interval === 0) {
            echo '░';
            ++$p;
        }

        continue;
    }

    $item = \json_decode(\file_get_contents(__DIR__ . '/itemmanagement/' . $item . '/info.json'));

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 1;
    $request->setData('number', (string) $item['number']);
    $request->setData('purchaseprice', (string) ($item['purchaseprice'] ?? ''));
    $request->setData('salesprice', $item['payment']['price'][0]['value'] ?? '');

    $module->apiItemCreate($request, $response);
    ++$apiCalls;

    $itemId = $response->get('')['response']->getId();

    // create prices
    foreach (($item['payment']['price'] ?? []) as $price) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('item', $itemId);
        $request->setData('price', $price['value']);
        $request->setData('currency', $price['currency']);

        $module->apiItemPriceCreate($request, $response);
        ++$apiCalls;
    }

    foreach ($item['l11ns'] as $name => $l11ns) {
        $l11nType = $l11nTypes[$name];

        foreach ($l11ns as $language => $l11n) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = 1;
            $request->setData('item', $itemId);
            $request->setData('type', $l11nType->getId());
            $request->setData('language', (string) $language);
            $request->setData('description', (string) $l11n);

            $module->apiItemL11nCreate($request, $response);
            ++$apiCalls;
        }
    }

    foreach ($item['attributes'] as $attribute) {
        $attrType = $attributeTypes[$name];

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('item', $itemId);
        $request->setData('type', $attrType->getId());

        if ($attribute['custom'] ?? true) {
            $request->setData('custom', $attribute['value']);
        } else {
            $request->setData('value', findAttributeIdByValue($attrType->getDefaults(), $attribute['value']));
        }

        $module->apiItemAttributeCreate($request, $response);
        ++$apiCalls;
    }

    foreach ($item['variants'] as $variant) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('number', (string) $variant['number']);
        $request->setData('purchaseprice', (string) ($variant['purchaseprice'] ?? ''));
        $request->setData('salesprice', $variant['payment']['price'][0]['value'] ?? '');
        $request->setData('parent', $itemId);

        $module->apiItemCreate($request, $response);
        ++$apiCalls;

        $variantId = $response->get('')['response']->getId();

        // create prices
        foreach (($variant['payment']['price'] ?? []) as $price) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = 1;
            $request->setData('item', $variantId);
            $request->setData('price', $price['value']);
            $request->setData('currency', $price['currency']);

            $module->apiItemPriceCreate($request, $response);
            ++$apiCalls;
        }

        foreach ($variant['l11ns'] as $name => $l11ns) {
            $l11nType = $l11nTypes[$name];

            foreach ($l11ns as $language => $l11n) {
                $response = new HttpResponse();
                $request  = new HttpRequest(new HttpUri(''));

                $request->header->account = 1;
                $request->setData('item', $variantId);
                $request->setData('type', $l11nType->getId());
                $request->setData('language', (string) $language);
                $request->setData('description', (string) $l11n);

                $module->apiItemL11nCreate($request, $response);
                ++$apiCalls;
            }
        }

        foreach ($variant['attributes'] as $attribute) {
            $attrType = $attributeTypes[$name];

            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = 1;
            $request->setData('item', $variantId);
            $request->setData('type', $attrType->getId());

            if ($attribute['custom'] ?? true) {
                $request->setData('custom', $attribute['value']);
            } else {
                $request->setData('value', findAttributeIdByValue($attrType->getDefaults(), $attribute['value']));
            }

            $module->apiItemAttributeCreate($request, $response);
            ++$apiCalls;
        }
    }

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

unset($attributes);
unset($itemAttrType);
unset($itemAttrValue);

unset($attributeTypes);
unset($attributeValues);
unset($l11nTypes);

echo \str_repeat('░', 10 - $p);
