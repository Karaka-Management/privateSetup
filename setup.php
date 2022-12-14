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
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\DataStorage\Session\HttpSession;
use phpOMS\Dispatcher\Dispatcher;
use phpOMS\Event\EventManager;
use phpOMS\Log\FileLogger;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestMethod;
use phpOMS\Module\ModuleManager;
use phpOMS\Router\WebRouter;
use phpOMS\System\File\Local\Directory;
use phpOMS\System\SystemUtils;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;

$ASYNC                = ($argv[1] ?? '') === '-a';
$MODULE_INDEDX        = (int) ($argv[2] ?? 0);
$syncModulesToInstall = $ASYNC ? 9 : null;

//region Setup
$config    = require_once __DIR__ . '/config.php';
$variables = require_once __DIR__ . '/variables.php';

// Get directory size (recursive)
function dirSize() : float
{
    $size = 0;
    $f    = [
        \realpath(__DIR__ . '/../Web'),
        \realpath(__DIR__ . '/../Modules/Media/Files'),
        \realpath(__DIR__ . '/../phpOMS'),
        \realpath(__DIR__ . '/../jsOMS'),
        \realpath(__DIR__ . '/../cssOMS'),
        \realpath(__DIR__ . '/../Resources'),
    ];

    foreach ($f as $dir) {
        $io    = \popen('/usr/bin/du -sk ' . $dir, 'r');
        $tSize = \fgets($io, 4096);
        $tSize = \substr($tSize, 0, \strpos($tSize, "\t"));
        \pclose($io);

        $size += (int) $tSize;
    }

    return (float) ($size / 1024);
}

// Get file count (recursive)
function dirCount() : int
{
    $count = 0;
    $f     = [
        \realpath(__DIR__ . '/../Web'),
        \realpath(__DIR__ . '/../Modules/Media/Files'),
        \realpath(__DIR__ . '/../phpOMS'),
        \realpath(__DIR__ . '/../jsOMS'),
        \realpath(__DIR__ . '/../cssOMS'),
        \realpath(__DIR__ . '/../Resources'),
    ];

    foreach ($f as $dir) {
        $io     = \popen('/usr/bin/find ' . $dir . ' -type f | wc -l', 'r');
        $tCount = \fgets($io, 4096);
        $tCount = \trim($tCount, "\t\r\n");
        \pclose($io);

        $count += (int) $tCount;
    }

    return (int) $count;
}

// Get database rows (reliable)
function getDatabaseRows($con, string $dbname) : int
{
    $sth    = $con->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '" . $dbname ."' AND table_type = 'BASE TABLE';");
    $tables = $sth->fetchAll(\PDO::FETCH_COLUMN, 0);

    $rows = 0;
    foreach ($tables as $table) {
        $sth   = $con->query('SELECT COUNT(*) FROM ' . $dbname . '.' . $table);
        $rows += $sth->fetch(\PDO::FETCH_COLUMN, 0);
    }

    return $rows;
}

// Check writing permissions
if (\exec('whoami') !== 'www-data'
    || !\is_writable(__DIR__ . '/../Modules')
    || !\is_writable(__DIR__ . '/../Modules/Media/Files')
    || !\is_writable(__DIR__ . '/../Web')
) {
    echo "Not sufficient permissions or not running as www-data.\n";

    exit(-1);
}

if (!$ASYNC || $MODULE_INDEDX < 1) {
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

    $request->setData('orgname', 'Jingga');
    $request->setData('adminname', 'admin');
    $request->setData('adminpassword', 'orange');
    $request->setData('adminemail', 'admin@jingga.app');
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
}

// Setup installer for api calls
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

/** @var \phpOMS\DataStorage\Database\Connection\ConnectionAbstract $con */
$con = $app->dbPool->get('admin');
DataMapperFactory::db($con);

$app->orgId          = 1;
$app->appName        = 'Backend';
$app->accountManager = new AccountManager(new HttpSession());
$app->l11nServer     = LocalizationMapper::get()->where('id', 1)->execute();
$app->appSettings    = new CoreSettings();
$app->moduleManager  = new ModuleManager($app, __DIR__ . '/../Modules/');
$app->dispatcher     = new Dispatcher($app);
$app->eventManager   = new EventManager($app->dispatcher);
$app->eventManager->importFromFile(__DIR__ . '/../Web/Api/Hooks.php');

$account = new Account();
TestUtils::setMember($account, 'id', 1);

$permission = new AccountPermission();
$permission->setUnit(1);
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

$app->router = new WebRouter();
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

$tldOrg     = 1;
$defaultOrg = 1;

if (!$ASYNC || $MODULE_INDEDX < 1) {
    $config = include __DIR__ . '/../Install/Templates/config.tpl.php';
    \file_put_contents(__DIR__ . '/../config.php', $config);
}
//endregion

// Setup query for database size
$dbSizeQuery = 'SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) "size"  FROM information_schema.tables where table_schema = "' . $dbname . '";';
$sizeOld     = 0.0;
$sizeNew     = (float) $con->con->query($dbSizeQuery)->fetch()['size'];

// Setup query for database row count
$rowOld = 0;
$rowNew = \getDatabaseRows($con->con, $dbname);

// Set base directory size
$dirOld = 0;
$dirNew = \dirSize();

// Set base directory count
$dirCountOld = 0;
$dirCountNew = \dirCount();

// Set simulated API call counter
$apiOld   = 0;
$apiCalls = 0;

// Install scripts for the different module demos
$toInstall = [
    __DIR__ . '/setupModules.php'                 => 'Module',
    __DIR__ . '/setupGroups.php'                  => 'Group',
    __DIR__ . '/setupOrganization.php'            => 'Organization',
    __DIR__ . '/setupAccounts.php'                => 'Account',
    __DIR__ . '/setupOnlineResourceWatcher.php'   => 'OnlineResourceWatcher',
    __DIR__ . '/setupCMS.php'                     => 'CMS',
];

$toInstallCount = \count($toInstall);

$syncToInstall = [];
if ($ASYNC && $MODULE_INDEDX < ((int) $syncModulesToInstall)) {
    $syncToInstall = $toInstall;
} elseif ($ASYNC && $MODULE_INDEDX >= ((int) $syncModulesToInstall)) {
    $syncToInstall = \array_slice($toInstall, $MODULE_INDEDX - 1, 1);
} else {
    $syncToInstall = $toInstall;
}

if (!$ASYNC || $MODULE_INDEDX < ((int) $syncModulesToInstall)) {
    echo "\n";
    FileLogger::startTimeLog('total');

    echo ' -----------------------------------------------------------------------------------------------------------------------' , "\n";
    echo '| ' , \sprintf('%-25s', 'Section')
        , '| ' , \sprintf('%-11s', 'Progress')
        , '| ' , \sprintf('%-9s', 'ExecTime')
        , '| ' , \sprintf('%-9s', 'APICalls')
        , '| ' , \sprintf('%-9s', 'DbSize')
        , '| ' , \sprintf('%-9s', 'DbRows')
        , '| ' , \sprintf('%-11s', 'DirSize')
        , '| ' , \sprintf('%-9s', 'DirCount')
        , '| ' , \sprintf('%-10s', 'Mem.')
        , "|\n";
    echo '|==========================|============|==========|==========|==========|==========|============|==========|===========|' , "\n";
}

$installCounter = 0 + $MODULE_INDEDX;
foreach ($syncToInstall as $path => $title) {
    ++$installCounter;

    if ($ASYNC && $MODULE_INDEDX === 0 && $installCounter > ((int) $syncModulesToInstall)) {
        SystemUtils::runProc(
            'php',
            __DIR__ . '/setup.php -a ' . $installCounter,
            true
        );

        continue;
    }

    \gc_collect_cycles();

    if ($ASYNC) {
        \ob_start();
    }

    echo '| ' , \sprintf('%-25s', $title) , '| ';

    FileLogger::startTimeLog('section');

    $sizeOld = $sizeNew; $rowOld = $rowNew; $dirOld = $dirNew; $dirCountOld = $dirCountNew; $apiOld = $apiCalls;

    include $path;

    $time = \round(FileLogger::endTimeLog('section'), 2);
    $time = $time > 60.0 ? \round($time / 60, 2) . 'm' : $time . 's';

    $dbSize = \round(($sizeNew = (float) $con->con->query($dbSizeQuery)->fetch()['size']) - $sizeOld, 2) . 'MB';
    $dbRow  = ($rowNew = \getDatabaseRows($con->con, $dbname)) - $rowOld;

    $dirSize  = \round(($dirNew = \dirSize()) - $dirOld, 2) . 'MB';
    $dirCount = ($dirCountNew = \dirCount()) - $dirCountOld;

    echo ' | ' , \sprintf('%-9s', $time)
        , '| ' , \sprintf('%-9s', $apiCalls - $apiOld)
        , '| ' , \sprintf('%-9s', $dbSize)
        , '| ' , \sprintf('%-9s', $dbRow)
        , '| ' , \sprintf('%-11s', $dirSize)
        , '| ' , \sprintf('%-9s', $dirCount)
        , '| ' , \sprintf('%-10s', \round(\memory_get_usage() / 1048576, 2) . 'MB')
        , "|\n";

    echo $installCounter < $toInstallCount || $ASYNC
        ? '|--------------------------|------------|----------|----------|----------|----------|------------|----------|-----------|' . "\n"
        : '|==========================|============|==========|==========|==========|==========|============|==========|===========|' . "\n";

    if ($ASYNC) {
        \ob_end_flush();
    }
}

if (!$ASYNC) {
    $time     = \round(FileLogger::endTimeLog('total') / 60, 2) . 'm';
    $dbSize   = $con->con->query($dbSizeQuery)->fetch()['size'] . 'MB';
    $dbRow    = \getDatabaseRows($con->con, $dbname);
    $dirSize  = \round(\dirSize(), 2) . 'MB';
    $dirCount = \dirCount();

    echo '| ' , \sprintf('%-38s', 'Total')
        , '| ' , \sprintf('%-9s', $time)
        , '| ' , \sprintf('%-9s', $apiCalls)
        , '| ' , \sprintf('%-9s', $dbSize)
        , '| ' , \sprintf('%-9s', $dbRow)
        , '| ' , \sprintf('%-11s', $dirSize)
        , '| ' , \sprintf('%-9s', $dirCount)
        , '| ' , \sprintf('%-10s', \round(\memory_get_peak_usage() / 1048576, 2) . 'MB') . "|\n";
    echo ' -----------------------------------------------------------------------------------------------------------------------' , "\n\n";
} else {
    echo '| Running async. install scripts ...                                                                                    |' . "\n";
    echo '|--------------------------|------------|----------|----------|----------|----------|------------|----------|-----------|' . "\n";
}
