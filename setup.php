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

\ini_set('memory_limit', '2048M');
\ini_set('display_errors', '1');
\ini_set('display_startup_errors', '1');
\error_reporting(\E_ALL);

// For seeded test environment
//\mt_srand(200);

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
use phpOMS\System\File\Local\Directory;

//region Setup
$config    = require_once __DIR__ . '/config.php';
$variables = require_once __DIR__ . '/variables.php';

function dirSize() : float
{
    $size = 0;
    $f = [
        \realpath(__DIR__ . '/../Web'),
        \realpath(__DIR__ . '/../Modules/Media/Files'),
        \realpath(__DIR__ . '/../phpOMS'),
        \realpath(__DIR__ . '/../jsOMS'),
        \realpath(__DIR__ . '/../cssOMS'),
        \realpath(__DIR__ . '/../Resources'),
    ];

    foreach ($f as $dir) {
        $io = \popen('/usr/bin/du -sk ' . $dir, 'r');
        $tSize = \fgets($io, 4096);
        $tSize = \substr($tSize, 0, \strpos($tSize, "\t"));
        \pclose($io);

        $size += (int) $tSize;
    }

    return (float) ($size / 1024);
}

function dirCount() : int
{
    $count = 0;
    $f = [
        \realpath(__DIR__ . '/../Web'),
        \realpath(__DIR__ . '/../Modules/Media/Files'),
        \realpath(__DIR__ . '/../phpOMS'),
        \realpath(__DIR__ . '/../jsOMS'),
        \realpath(__DIR__ . '/../cssOMS'),
        \realpath(__DIR__ . '/../Resources'),
    ];

    foreach ($f as $dir) {
        $io = \popen('/usr/bin/find ' . $dir . ' -type f | wc -l', 'r');
        $tCount = \fgets($io, 4096);
        $tCount = \trim($tCount, "\t\r\n");
        \pclose($io);

        $count += (int) $tCount;
    }

    return (int) $count;
}

Directory::delete(__DIR__ . '/../Modules/Media/Files');
\mkdir(__DIR__ . '/../Modules/Media/Files', 0755);

// Reset database
$con = new \PDO($config['db']['core']['masters']['admin']['db'] . ':host=' .
    $config['db']['core']['masters']['admin']['host'],
    $config['db']['core']['masters']['admin']['login'],
    $config['db']['core']['masters']['admin']['password']
);
$con->exec('DROP DATABASE IF EXISTS ' . $config['db']['core']['masters']['admin']['database']);
$con->exec('CREATE DATABASE IF NOT EXISTS ' . $config['db']['core']['masters']['admin']['database']);

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

$dbSizeQuery = 'SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) "size"  FROM information_schema.tables where table_schema = "' . $dbname . '";';
$sizeOld = 0.0;
$sizeNew = (float) $con->query($dbSizeQuery)->fetch()['size'];

$dbRowQuery = 'SELECT SUM(table_rows) "rows" FROM information_schema.tables WHERE table_schema = "' . $dbname . '";';
$rowOld = 0;
$rowNew = (int) $con->query($dbRowQuery)->fetch()['rows'];

$dirOld = 0;
$dirNew = dirSize();

$dirCountOld = 0;
$dirCountNew = dirCount();

$toInstall = [
    __DIR__ . '/setupDemoTemplates.php' => 'Template',
    __DIR__ . '/setupModules.php' => 'Module',
    __DIR__ . '/setupMonitoring.php' => 'Monitoring',
    __DIR__ . '/setupGroups.php' => 'Group',
    __DIR__ . '/setupOrganization.php' => 'Organization',
    __DIR__ . '/setupAccounts.php' => 'Account',
    __DIR__ . '/setupExchange.php' => 'Exchange',
    __DIR__ . '/setupTag.php' => 'Tag',
    __DIR__ . '/setupMedia.php' => 'Media',
    __DIR__ . '/setupSurveys.php' => 'Surveys',
    __DIR__ . '/setupDashboard.php' => 'Dashboard',
    __DIR__ . '/setupContractManagement.php' => 'ContractManagement',
    __DIR__ . '/setupKanban.php' => 'Kanban',
    __DIR__ . '/setupQA.php' => 'QA',
    __DIR__ . '/setupEditor.php' => 'Editor',
    __DIR__ . '/setupTask.php' => 'Task',
    __DIR__ . '/setupNews.php' => 'News',
    __DIR__ . '/setupHelper.php' => 'Helper',
    __DIR__ . '/setupCMS.php' => 'CMS',
    __DIR__ . '/setupHumanResourceManagement.php' => 'HumanResourceManagement',
    __DIR__ . '/setupKnowledgebase.php' => 'Knowledgebase',
    __DIR__ . '/setupSupport.php' => 'Support',
    __DIR__ . '/setupDatabaseEditor.php' => 'DatabaseEditor',
    __DIR__ . '/setupCalendar.php' => 'Calendar',
    __DIR__ . '/setupItemManagement.php' => 'ItemManagement',
    __DIR__ . '/setupClientManagement.php' => 'ClientManagement',
    __DIR__ . '/setupSupplierManagement.php' => 'SupplierManagement',
    __DIR__ . '/setupWarehouseManagement.php' => 'WarehouseManagement',
    __DIR__ . '/setupBillingSuppliers.php' => 'Billing Suppliers',
    __DIR__ . '/setupBillingClients.php' => 'Billing Clients',
];

$toInstallCount = \count($toInstall);

echo "\n";
FileLogger::startTimeLog('total');

echo ' ------------------------------------------------------------------------------------------------------------' . "\n";
echo '| ' . \sprintf('%-25s', 'Section') . '| ' . \sprintf('%-11s', 'Progress') . '| ' . \sprintf('%-9s', 'ExecTime') . '| ' . \sprintf('%-9s', 'DbSize') . '| ' . \sprintf('%-9s', 'DbRows') . '| ' . \sprintf('%-11s', 'DirSize') . '| ' . \sprintf('%-9s', 'DirCount') . '| ' . \sprintf('%-10s', 'Mem.') . "|\n";
echo '|==========================|============|==========|==========|==========|============|==========|===========|' . "\n";

$installCounter = 0;
foreach ($toInstall as $path => $title) {
    ++$installCounter;

    \gc_collect_cycles();

    echo '| ' . \sprintf('%-25s', $title) . '| ';
    FileLogger::startTimeLog('section');
    include $path;
    DataMapperAbstract::clearCache();
    $time = \round(FileLogger::endTimeLog('section'), 2);
    $time = $time > 60.0 ? \round($time / 60, 2) . 'm' : $time . 's';
    $sizeOld = $sizeNew; $rowOld = $rowNew; $dirOld = $dirNew; $dirCountOld = $dirCountNew;
    $dbSize = \round(($sizeNew = (float) $con->query($dbSizeQuery)->fetch()['size']) - $sizeOld, 2) . 'MB';
    $dbRow = ($rowNew = (int) $con->query($dbRowQuery)->fetch()['rows']) - $rowOld;
    $dirSize = \round(($dirNew = dirSize()) - $dirOld, 2) . 'MB';
    $dirCount = ($dirCountNew = dirCount()) - $dirCountOld;
    echo ' | ' . \sprintf('%-9s', $time) . '| ' . \sprintf('%-9s', $dbSize) . '| ' . \sprintf('%-9s', $dbRow) . '| ' . \sprintf('%-11s', $dirSize) . '| ' . \sprintf('%-9s', $dirCount) . '| ' . \sprintf('%-10s', \round(\memory_get_usage() / 1048576, 2) . 'MB') . "|\n";

    echo $installCounter < $toInstallCount
        ? '|--------------------------|------------|----------|----------|----------|------------|----------|-----------|' . "\n"
        : '|==========================|============|==========|==========|==========|============|==========|===========|' . "\n";
}

$time = \round(FileLogger::endTimeLog('total') / 60, 2) . 'm';
$dbSize = $con->query($dbSizeQuery)->fetch()['size'] . 'MB';
$dbRow = (int) $con->query($dbRowQuery)->fetch()['rows'];
$dirSize = \round(dirSize(), 2) . 'MB';
$dirCount = dirCount();

echo '| ' . \sprintf('%-38s', 'Total') . '| ' . \sprintf('%-9s', $time) . '| ' . \sprintf('%-9s', $dbSize) . '| ' . \sprintf('%-9s', $dbRow) . '| ' . \sprintf('%-11s', $dirSize) . '| ' . \sprintf('%-9s', $dirCount) . '| ' . \sprintf('%-10s', \round(\memory_get_peak_usage() / 1048576, 2) . 'MB') . "|\n";
echo ' ------------------------------------------------------------------------------------------------------------' . "\n\n";
