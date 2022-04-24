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

use Modules\Billing\Models\BillTransferType;
use Modules\Billing\Models\BillTypeMapper;
use Modules\ClientManagement\Models\ClientMapper;
use Modules\ItemManagement\Models\ItemMapper;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\DateTime;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Setup news module
 *
 * @var \Modules\Billing\Controller\ApiController $module
 */
//region Billing
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\Billing\Controller\ApiController $module */
$module = $app->moduleManager->get('Billing');
TestUtils::setMember($module, 'app', $app);

// create invoice types

/** @var \Modules\Billing\Models\BillType[] $BILL_TYPES */
$BILL_TYPES       = BillTypeMapper::getAll()->execute();
$SALES_BILL_TYPES = [];

foreach ($BILL_TYPES as $type) {
    if ($type->transferType === BillTransferType::SALES) {
        $SALES_BILL_TYPES[] = $type->getId();
    }
}

$ITEM_COUNT       = ItemMapper::count()->execute();
$LOREM_COUNT      = \count(Text::LOREM_IPSUM) - 1;
$CUSTOMER_COUNT   = ClientMapper::count()->execute();
$INVOICES         = 15 * $CUSTOMER_COUNT;

$count    = $INVOICES;
$interval = (int) \ceil($count / 10);
$z        = 0;
$p        = 0;

for ($i = 0; $i < $INVOICES; ++$i) {
    if (\mt_rand(1, 100) <= 10) {
        continue;
    }

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = $aId = \mt_rand(2, 5);
    $request->setData('client', \mt_rand(1, $CUSTOMER_COUNT));
    $request->setData('address', null);
    $request->setData('type', $type = $SALES_BILL_TYPES[\mt_rand(0, \count($SALES_BILL_TYPES) - 1)]);
    $request->setData('status', null); // null = system settings, value = individual input
    $request->setData('performancedate', DateTime::generateDateTime(new \DateTime('2015-01-01'), new \DateTime('now'))->format('Y-m-d H:i:s'));
    $request->setData('sales_referral', null); // who these sales belong to
    $request->setData('shipping_terms', 1); // e.g. incoterms
    $request->setData('shipping_type', 1);
    $request->setData('shipping_cost', null);
    $request->setData('insurance_type', 1);
    $request->setData('insurance_cost', null); // null = system settings, value = individual input
    $request->setData('info', null); // null = system settings, value = individual input
    $request->setData('currency', null); // null = system settings, value = individual input
    $request->setData('payment', null); // null = system settings, value = individual input
    $request->setData('payment_terms', null); // null = system settings, value = individual input

    // promotion keys (only one or multiple?)
    $module->apiBillCreate($request, $response);
    ++$apiCalls;
    $bId = $response->get('')['response']->getId();

    switch ($type) {
        case 1:
        default:
    }

    // create type (offer, order confirmation, delivery note, invoice)
    // if invoice probability of delivery note or order confirmation or offer
    // if delivery note probability of order confirmation or offer

    $ITEMS = \mt_rand(1, 10);

    for ($k = 0; $k < $ITEMS; ++$k) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = $aId;

        $iId = \mt_rand(0, $ITEM_COUNT);

        $request->setData('bill', $bId);
        $request->setData('item', $iId === 0 ? null : $iId);

        if ($iId === 0) {
            // @todo: add text
        }

        $request->setData('quantity', \mt_rand(1, 11));
        $request->setData('tax', null);
        $request->setData('text', $iId === 0 ? Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)] : null);

        // discounts
        if (\mt_rand(1, 100) < 31) {
            $request->setData('discount_percentage', \mt_rand(5, 30));
        }

        $module->apiBillElementCreate($request, $response);
        ++$apiCalls;
    }

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = $aId;
    $request->setData('bill', $bId);

    $module->apiBillPdfArchiveCreate($request, $response);
    ++$apiCalls;

    //region note
    for ($k = 0; $k < 10; ++$k) {
        if (\mt_rand(1, 100) < 76) {
            continue;
        }

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);

        $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');

        $request->setData('id', $bId);
        $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
        $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));

        $module->apiNoteCreate($request, $response);
        ++$apiCalls;
    }
    //endregion

    //region files
    $files = \scandir(__DIR__ . '/media/types');

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = \mt_rand(2, 5);

    $fileCounter = 0;
    $toUpload    = [];
    $mFiles      = [];
    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === 'Video.mp4' || \mt_rand(1, 100) < 76) {
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
            $mFiles[] = \mt_rand(1, 9);
        }
    }

    if (!empty($toUpload)) {
        TestUtils::setMember($request, 'files', $toUpload);
    }

    if (!empty($mFiles)) {
        $request->setData('media', \json_encode(\array_unique($mFiles)));
    }

    if (empty($toUpload) && empty($mFiles)) {
        continue;
    }

    $request->setData('bill', $bId);
    $module->apiMediaAddToBill($request, $response);
    ++$apiCalls;
    //endregion

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

unset($SALES_BILL_TYPES);
unset($BILL_TYPES);

echo \str_repeat('░', 10 - $p);
