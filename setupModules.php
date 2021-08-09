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

use Modules\Admin\Models\ModuleStatusUpdateType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\System\File\Local\Directory;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;

/**
 * Install modules
 *
 * @var \Modules\Admin\Controller\ApiController $module
 */
//region ModuleInstall
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Admin');
TestUtils::setMember($module, 'app', $app);

$response                 = new HttpResponse();
$request                  = new HttpRequest(new HttpUri(''));
$request->header->account = 1;
$request->setData('status', ModuleStatusUpdateType::INSTALL);

$toInstall = [
    'Monitoring', 'Helper', 'Search', 'Dashboard', 'Media', 'Tasks', 'Messages', 'Calendar', 'Editor', 'DatabaseEditor', 'CMS', 'Checklist', 'Surveys',
    'News', 'Comments', 'Profile', 'Kanban', 'QA', 'Workflow', 'Job', 'HumanResourceManagement', 'HumanResourceTimeRecording', 'MyPrivate', 'ContractManagement',
    'Support', 'Sales', 'ClientManagement', 'Accounting', 'Purchase', 'SupplierManagement', 'ItemManagement', 'Billing', 'InvoiceManagement',
    'WarehouseManagement', 'StockTaking', 'Shop', 'QualityManagement', 'AssetManagement', 'Marketing', 'Knowledgebase', 'Exchange',
];

$count = \count($toInstall);
$interval = (int) \ceil($count / 10);
$c = 0;
$p = 0;

foreach ($toInstall as $install) {
    $request->setData('module', $install, true);
    $module->apiModuleStatusUpdate($request, $response);

    ++$c;
    if ($c % $interval === 0) {
    	echo '░';
    	++$p;
    }
}

$app->eventManager->importFromFile(__DIR__ . '/../Web/Api/Hooks.php');
//endregion

echo \str_repeat('░', 10 - $p);
