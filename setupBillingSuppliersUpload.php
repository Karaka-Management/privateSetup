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

use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;

/**
 * Setup news module
 */
//region Billing
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\Billing\Controller\ApiController $module */
$module = $app->moduleManager->get('Billing');
TestUtils::setMember($module, 'app', $app);

$count    = 100;
$interval = (int) \ceil($count / 10);
$z        = 0;
$p        = 0;

$tmpInvoices = \scandir(__DIR__ . '/billing');
$invoiceDocs = [];
foreach ($tmpInvoices as $invoice) {
    if ($invoice !== '..' && $invoice !== '.') {
        $invoiceDocs[] = $invoice;
    }
}

for ($i = 0; $i < $count; ++$i) {
    $toUpload = [];
    $file = $invoiceDocs[\mt_rand(0, \count($invoiceDocs) - 1)];

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = \mt_rand(2, 5);

    \copy(__DIR__ . '/billing/' . $file, __DIR__ . '/temp/' . $file);

    $toUpload['file0'] = [
        'name'     => $file,
        'type'     => \explode('.', $file)[1],
        'tmp_name' => __DIR__ . '/temp/' . $file,
        'error'    => \UPLOAD_ERR_OK,
        'size'     => \filesize(__DIR__ . '/temp/' . $file),
    ];

    TestUtils::setMember($request, 'files', $toUpload);

    $module->apiSupplierBillUpload($request, $response);
    ++$apiCalls;

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

unset($PURCHASE_BILL_TYPES);
unset($tmpInvoices);
unset($invoiceDocs);
unset($BILL_TYPES);

echo \str_repeat('░', 10 - $p);
