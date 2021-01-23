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

use Modules\ClientManagement\Models\ClientMapper;
use Modules\ItemManagement\Models\ItemMapper;
use Modules\Billing\Models\BillTypeMapper;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;
use phpOMS\Utils\RnG\DateTime;

/**
 * Setup news module
 *
 * @var \Modules\Billing\Controller\ApiController $module
 */
//region Billing
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Billing');
TestUtils::setMember($module, 'app', $app);

// create invoice types

$BILL_TYPES = BillTypeMapper::getAll();
$BILL_TYPES_COUNT = \count($BILL_TYPES);
$ITEM_COUNT = ItemMapper::count();
$LOREM_COUNT = \count(Text::LOREM_IPSUM) - 1;
$CUSTOMER_COUNT = ClientMapper::count();
$INVOICES = 150 * $CUSTOMER_COUNT;

// todo: better customer order randomization, currently not realistic (looping all customers instead of random order)

for ($i = 0; $i < $INVOICES; ++$i) {
    if (\mt_rand(1, 100) <= 10) {
        continue;
    }

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = $aId = \mt_rand(2, 5);
    $request->setData('client', \mt_rand(1, $CUSTOMER_COUNT));
    $request->setData('address', null);
    $request->setData('type', $type = \mt_rand(1, $BILL_TYPES_COUNT));
    $request->setData('status', null); // null = system settings, value = individual input
    $request->setData('date', DateTime::generateDateTime(new \DateTime('2015-01-01'), new \DateTime('now')));
    $request->setData('sales_referral', null); // who these sales belong to
    $request->setData('shipping_terms', 1); // e.g. incoterms
    $request->setData('shipping_type', 1); // @todo consider to create general cost type for many different costs (e.g. banking fees etc.)
    $request->setData('shipping_cost', null);
    $request->setData('insurance_type', 1);
    $request->setData('insurance_cost', null); // null = system settings, value = individual input
    $request->setData('info', null); // null = system settings, value = individual input
    $request->setData('currency', null); // null = system settings, value = individual input
    $request->setData('payment', null); // null = system settings, value = individual input
    $request->setData('payment_terms', null); // null = system settings, value = individual input

    // promotion keys (only one or multiple?)
    $module->apiBillCreate($request, $response);
    $bId = $response->get('')['response']->getId();

    switch ($type) {
        case 1:
        default:
    }

    // create type (offer, order confirmation, delivery note, invoice)
    // if invoice probability of delivery note or order confirmation or offer
    // if delivery note probability of order confirmation or offer

    $ITEMS = \mt_rand(1, 15);

    for ($k = 0; $k < $ITEMS; ++$k) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = $aId;

        $iId = \mt_rand(0, $ITEM_COUNT);

        $request->setData('bill', $bId);
        $request->setData('item', $iId === 0 ? null : $iId);
        $request->setData('quantity', \mt_rand(1, 4));
        $request->setData('price', null);
        $request->setData('discount_type', null);
        $request->setData('discount', null);
        $request->setData('tax', null);
        $request->setData('text', $iId === 0 ? Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)] : null);

        $module->apiBillElementCreate($request, $response);
    }
}

unset($BILL_TYPES);
