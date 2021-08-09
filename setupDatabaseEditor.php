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

use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;
use phpOMS\DataStorage\Database\DatabaseType;

/**
 * Setup news module
 *
 * @var \Modules\DatabaseEditor\Controller\ApiController $module
 */
//region DatabaseEditor
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('DatabaseEditor');
TestUtils::setMember($module, 'app', $app);

$QUERIES = [
	[
		'title' => 'Orange-Management Accounts',
		'con' => [
			'type' => DatabaseType::MYSQL,
			'host' => '127.0.0.1',
			'port' => '3306',
			'db' => 'oms',
			'login' => 'root',
			'password' => 'root',
		],
		'query' => 'SELECT account_id, account_login, account_name1, account_name2, account_email FROM account WHERE account_login != \'\';',
		'result' => \file_get_contents(__DIR__ . '/databaseeditor/accounts.csv')
	],
	[
		'title' => 'Sqlite Database Countries',
		'con' => [
			'type' => DatabaseType::SQLITE,
			'host' => __DIR__ . '/../phpOMS/Localization/Defaults/localization.sqlite',
			'port' => '',
			'db' => '',
			'login' => '',
			'password' => '',
		],
		'query' => 'SELECT * FROM country;',
		'result' => \file_get_contents(__DIR__ . '/databaseeditor/countries.csv')
	]
];

foreach ($QUERIES as $query) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = \mt_rand(1, 5);
    $request->setData('title', $query['title']);
    $request->setData('type', $query['con']['type']);
    $request->setData('host', $query['con']['host']);
    $request->setData('port', $query['con']['port']);
    $request->setData('db', $query['con']['db']);
    $request->setData('login', $query['con']['login']);
    $request->setData('password', $query['con']['password']);
    $request->setData('query', $query['query']);
    $request->setData('result', $query['result']);

    $module->apiQueryCreate($request, $response);
}
//endregion

echo '░░░░░░░░░░';