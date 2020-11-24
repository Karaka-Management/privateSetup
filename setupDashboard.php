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

use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;

/**
 * Setup dashboard module
 *
 * @var \Modules\Dashboard\Controller\ApiController $module
 */
//region Dashboard
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Dashboard');
TestUtils::setMember($module, 'app', $app);

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 1;
$request->setData('title', 'TestBoard');
$module->apiBoardCreate($request, $response);

$request->setData('board', 1);
$request->setData('order', 1, true);
$request->setData('module', 'News', true);
$module->apiComponentCreate($request, $response);

$request->setData('order', 2, true);
$request->setData('module', 'Tasks', true);
$module->apiComponentCreate($request, $response);

$request->setData('order', 3, true);
$request->setData('module', 'Messages', true);
$module->apiComponentCreate($request, $response);

$request->setData('order', 4, true);
$request->setData('module', 'Calendar', true);
$module->apiComponentCreate($request, $response);
//endregion
