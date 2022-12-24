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

use Modules\Admin\Models\ModuleStatusUpdateType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;

/**
 * Install modules
 */
//region ModuleInstall
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\Admin\Controller\ApiController $module */
$module = $app->moduleManager->get('Admin');
TestUtils::setMember($module, 'app', $app);

$response                 = new HttpResponse();
$request                  = new HttpRequest(new HttpUri(''));
$request->header->account = 1;
$request->setData('status', ModuleStatusUpdateType::INSTALL);

$toInstall = [
    'Monitoring', 'Search', 'Dashboard', 'Media', 'CMS', 'Profile', 'Job', 'OnlineResourceWatcher', 'HumanResourceManagement', 'MyPrivate', 'ContractManagement',
    'Sales', 'ClientManagement', 'Purchase', 'SupplierManagement', 'ItemManagement', 'Billing', 'WarehouseManagement', 'Shop', 'Exchange',
];

$count    = \count($toInstall);
$interval = (int) \ceil($count / 10);
$c        = 0;
$p        = 0;

foreach ($toInstall as $install) {
    $request->setData('module', $install, true);
    $module->apiModuleStatusUpdate($request, $response);
    ++$apiCalls;

    ++$c;
    if ($c % $interval === 0) {
        echo '░';
        ++$p;
    }
}

$app->eventManager->importFromFile(__DIR__ . '/../Web/Api/Hooks.php');
//endregion

echo \str_repeat('░', 10 - $p);
