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
 * Setup human resource module
 *
 * @var \Modules\HumanResourceManagement\Controller\ApiController $module
 */
//region HumanResource
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('HumanResourceManagement');
TestUtils::setMember($module, 'app', $app);

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->getHeader()->setAccount(2);

$i = 0;
/** @var array $account */
foreach ($accounts as $account) {
    ++$i;

    $request->setData('profiles', $i, true);
    $module->apiEmployeeCreate($request, $response);
}

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->getHeader()->setAccount(2);

$request->setData('employee', 1, true);
$request->setData('start', '2015-07-01', true);
$request->setData('end', '2017-01-15', true);
$request->setData('unit', 2, true);
$request->setData('department', 13, true);
$request->setData('position', 31, true);
$module->apiEmployeeHistoryCreate($request, $response);

$request->setData('employee', 1, true);
$request->setData('start', '2017-01-15', true);
$request->setData('end', '2019-08-31', true);
$request->setData('unit', 2, true);
$request->setData('department', 13, true);
$request->setData('position', 9, true);
$module->apiEmployeeHistoryCreate($request, $response);

$request->setData('employee', 1, true);
$request->setData('start', '2017-09-01', true);
$request->setData('end', '2019-01-01', true);
$request->setData('unit', 2, true);
$request->setData('department', 13, true);
$request->setData('position', 8, true);
$module->apiEmployeeHistoryCreate($request, $response);

$request->setData('employee', 1, true);
$request->setData('start', '2019-01-01', true);
$request->setData('end', '', true);
$request->setData('unit', 2, true);
$request->setData('department', 13, true);
$request->setData('position', 7, true);
$module->apiEmployeeHistoryCreate($request, $response);
//endregion
