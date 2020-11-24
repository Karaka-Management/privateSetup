<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
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

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));
$request->header->account = 1;
$request->setData('status', ModuleStatusUpdateType::INSTALL);

$toInstall = [
    'Helper', 'Search', 'Dashboard', 'Media', 'Tasks', 'Messages', 'Calendar', 'Editor', 'CMS', 'Checklist', 'News', 'Comments',
    'Profile', 'Kanban', 'QA', 'Workflow', 'HumanResourceManagement', 'HumanResourceTimeRecording', 'MyPrivate',
    'Support', 'Sales', 'ClientManagement', 'Accounting', 'Purchase', 'SupplierManagement', 'ItemManagement', 'Billing', 'InvoiceManagement',
    'WarehouseManagement', 'StockTaking', 'QualityManagement', 'AssetManagement', 'Marketing', 'Knowledgebase', 'Exchange',
];

if (\is_dir(__DIR__ . '/../Modules/Media/Files')) {
    Directory::delete(__DIR__ . '/../Modules/Media/Files');

    \mkdir(__DIR__ . '/../Modules/Media/Files');
}

foreach ($toInstall as $install) {
    $request->setData('module', $install, true);
    $module->apiModuleStatusUpdate($request, $response);
}
//endregion
