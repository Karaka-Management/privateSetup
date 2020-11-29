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

\ini_set('memory_limit', '2048M');
\ini_set('display_errors', '1');
\ini_set('display_startup_errors', '1');
\error_reporting(\E_ALL);

require_once __DIR__ . '/../phpOMS/Autoloader.php';

/**
 * This script is usefull when you want to manually install the app without resetting an old database/app or new empty database.
 */
use Install\WebApplication;
use Model\CoreSettings;
use Modules\Admin\Models\AccountPermission;
use Modules\Admin\Models\LocalizationMapper;
use phpOMS\Account\Account;
use phpOMS\Account\AccountManager;
use phpOMS\Account\PermissionType;
use phpOMS\Application\ApplicationAbstract;
use phpOMS\DataStorage\Database\DatabasePool;
use phpOMS\DataStorage\Database\DataMapperAbstract;
use phpOMS\DataStorage\Session\HttpSession;
use phpOMS\Dispatcher\Dispatcher;
use phpOMS\Event\EventManager;
use phpOMS\Log\FileLogger;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestMethod;
use phpOMS\Module\ModuleManager;
use phpOMS\Router\WebRouter;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;

//region Setup
$config    = require_once __DIR__ . '/config.php';
$variables = require_once __DIR__ . '/variables.php';

// Reset database
$db = new \PDO($config['db']['core']['masters']['admin']['db'] . ':host=' .
    $config['db']['core']['masters']['admin']['host'],
    $config['db']['core']['masters']['admin']['login'],
    $config['db']['core']['masters']['admin']['password']
);
$db->exec('DROP DATABASE IF EXISTS ' . $config['db']['core']['masters']['admin']['database']);
$db->exec('CREATE DATABASE IF NOT EXISTS ' . $config['db']['core']['masters']['admin']['database']);

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));
$request->setMethod(RequestMethod::POST);

$request->setData('dbhost', $config['db']['core']['masters']['admin']['host']);
$request->setData('dbtype', $config['db']['core']['masters']['admin']['db']);
$request->setData('dbport', $config['db']['core']['masters']['admin']['port']);
$request->setData('dbprefix', $config['db']['core']['masters']['admin']['prefix']);
$request->setData('dbname', $config['db']['core']['masters']['admin']['database']);
$request->setData('schemauser', $config['db']['core']['masters']['admin']['login']);
$request->setData('schemapassword', $config['db']['core']['masters']['admin']['password']);
$request->setData('createuser', $config['db']['core']['masters']['admin']['login']);
$request->setData('createpassword', $config['db']['core']['masters']['admin']['password']);
$request->setData('selectuser', $config['db']['core']['masters']['admin']['login']);
$request->setData('selectpassword', $config['db']['core']['masters']['admin']['password']);
$request->setData('updateuser', $config['db']['core']['masters']['admin']['login']);
$request->setData('updatepassword', $config['db']['core']['masters']['admin']['password']);
$request->setData('deleteuser', $config['db']['core']['masters']['admin']['login']);
$request->setData('deletepassword', $config['db']['core']['masters']['admin']['password']);

$request->setData('orgname', 'Orange Management');
$request->setData('adminname', 'admin');
$request->setData('adminpassword', 'orange');
$request->setData('adminemail', 'admin@oms.com');
$request->setData('domain', '127.0.0.1');
$request->setData('websubdir',  $config['page']['root']);
$request->setData('defaultlang', 'en');
$request->setData('defaultcountry', 'us');

$request->setData(
    'apps',
    'Install/Application/Api, '
    . 'Install/Application/Backend, '
    . 'Install/Application/E404, '
    . 'Install/Application/E500, '
    . 'Install/Application/E503'
);

WebApplication::installRequest($request, $response);

// Setup for api calls
$app = new class() extends ApplicationAbstract
{
    protected string $appName = 'Api';
};

$app->dbPool = new DatabasePool();
$app->dbPool->create('admin', $config['db']['core']['masters']['admin']);
$app->dbPool->create('select', $config['db']['core']['masters']['select']);
$app->dbPool->create('update', $config['db']['core']['masters']['update']);
$app->dbPool->create('insert', $config['db']['core']['masters']['insert']);
$app->dbPool->create('schema', $config['db']['core']['masters']['schema']);

$app->orgId          = 2;
$app->appName        = 'Backend';
$app->accountManager = new AccountManager(new HttpSession());
$app->l11nServer     = LocalizationMapper::get(1);
$app->appSettings    = new CoreSettings($app->dbPool->get());
$app->moduleManager  = new ModuleManager($app, __DIR__ . '/../Modules/');
$app->dispatcher     = new Dispatcher($app);
$app->eventManager   = new EventManager($app->dispatcher);
$app->eventManager->importFromFile(__DIR__ . '/../Web/Api/Hooks.php');

$account = new Account();
TestUtils::setMember($account, 'id', 1);

$permission = new AccountPermission();
$permission->setUnit(2);
$permission->setApp('backend');
$permission->setPermission(
    PermissionType::READ
    | PermissionType::CREATE
    | PermissionType::MODIFY
    | PermissionType::DELETE
    | PermissionType::PERMISSION
);

$account->addPermission($permission);

$app->accountManager->add($account);

$account2 = new Account();
TestUtils::setMember($account2, 'id', 2);

$permission = new AccountPermission();
$permission->setUnit(2);
$permission->setApp('backend');
$permission->setPermission(
    PermissionType::READ
    | PermissionType::CREATE
    | PermissionType::MODIFY
    | PermissionType::DELETE
    | PermissionType::PERMISSION
);

$account2->addPermission($permission);

$app->accountManager->add($account2);
$app->router = new WebRouter();
//endregion

/**
 * Setup additional units
 *
 * @var \Modules\Organization\Controller\ApiController $module
 */
//region Unit
$module = $app->moduleManager->get('Organization');
TestUtils::setMember($module, 'app', $app);

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 1;
$request->setData('name', 'Lima');
$request->setData('parent', 1);
$request->setData('status', 1);
$request->setData('description', \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6'));
$module->apiUnitCreate($request, $response);
//endregion

/**
 * Change app settings
 *
 * @var \Modules\Admin\Controller\ApiController $module
 */
//region Settings
$configInstalled = include __DIR__ . '/../config.php';

$db     = $configInstalled['db']['core']['masters']['admin']['db'];
$host   = $configInstalled['db']['core']['masters']['admin']['host'];
$port   = $configInstalled['db']['core']['masters']['admin']['port'];
$dbname = $configInstalled['db']['core']['masters']['admin']['database'];

$admin  = ['login' => $configInstalled['db']['core']['masters']['admin']['login'], 'password' => $configInstalled['db']['core']['masters']['admin']['password']];
$insert = ['login' => $configInstalled['db']['core']['masters']['insert']['login'], 'password' => $configInstalled['db']['core']['masters']['insert']['password']];
$select = ['login' => $configInstalled['db']['core']['masters']['select']['login'], 'password' => $configInstalled['db']['core']['masters']['select']['password']];
$update = ['login' => $configInstalled['db']['core']['masters']['update']['login'], 'password' => $configInstalled['db']['core']['masters']['update']['password']];
$delete = ['login' => $configInstalled['db']['core']['masters']['delete']['login'], 'password' => $configInstalled['db']['core']['masters']['delete']['password']];
$schema = ['login' => $configInstalled['db']['core']['masters']['schema']['login'], 'password' => $configInstalled['db']['core']['masters']['schema']['password']];

$subdir = $configInstalled['page']['root'];
$tld    = \array_keys($configInstalled['app']['domains'])[0];

$tldOrg     = 2;
$defaultOrg = 2;

$config = include __DIR__ . '/../Install/Templates/config.tpl.php';
\file_put_contents(__DIR__ . '/../config.php', $config);
//endregion

echo "\n";
FileLogger::startTimeLog('total');

echo "Template setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupDemoTemplates.php';
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "Module setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupModules.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "Group setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupGroups.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "Organization setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupOrganization.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "Account setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupAccounts.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "Tag setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupTag.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "Dashboard setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupDashboard.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "Kanban setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupKanban.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "QA setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupQA.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "Editor setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupEditor.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "Task setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupTask.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "News setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupNews.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "Helper setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupHelper.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "CMS setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupCMS.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "ItemManagement setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupItemManagement.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "ClientManagement setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupClientManagement.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "SupplierManagement setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupSupplierManagement.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "HumanResourceManagement setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupHumanResourceManagement.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "Knowledgebase setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupKnowledgebase.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "Support setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupSupport.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";

echo "Calendar setup:\n";
FileLogger::startTimeLog('section');
include __DIR__ . '/setupCalendar.php';
DataMapperAbstract::clearCache();
echo "Time: " . \round(FileLogger::endTimeLog('section'), 2) . "s\n\n";
// include __DIR__ . '/setupMessenges.php';
// include __DIR__ . '/setupWarehouseing.php';
// include __DIR__ . '/setupBilling.php';

echo "Total: " . \round(FileLogger::endTimeLog('total') / 60, 2) . "m\n";