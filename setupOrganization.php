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

use Modules\Organization\Models\DepartmentMapper;
use Modules\Organization\Models\Status;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\System\MimeType;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;

/**
 * Setup departments
 *
 * @var \Modules\Organization\Controller\ApiController $module
 */
//region Department
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Organization');
TestUtils::setMember($module, 'app', $app);

$departmentIds = [];

foreach ($variables['departments'] as $key => $department) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->getHeader()->setAccount(1);
    $request->setData('name', $department['name']);
    $request->setData('status', Status::ACTIVE);
    $request->setData('unit', 2);
    $request->setData('parent', $departmentIds[$department['parent']] ?? null);
    $request->setData('description', \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6'));

    $module->apiDepartmentCreate($request, $response);
    $departmentIds[$department['name']]   = $response->get('')['response']->getId();
    $variables['departments'][$key]['id'] = $response->get('')['response']->getId();
}
//endregion

/**
 * Setup positions
 *
 * @var \Modules\Organization\Controller\ApiController $module
 */
//region Departments
$module = $app->moduleManager->get('Organization');
TestUtils::setMember($module, 'app', $app);

$departments = DepartmentMapper::getAll();
$postionIds  = [];
foreach ($variables['positions'] as $key => $position) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->getHeader()->setAccount(1);
    $request->setData('name', $position['name']);
    $request->setData('status', Status::ACTIVE);
    $request->setData('parent', $positionIds[$position['parent']] ?? null);
    $request->setData('description', \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6'));

    foreach ($departments as $d) {
        if (!isset($position['department']) || $d->getName() === $position['department']) {
            $request->setData('department', $d->getId());
            $module->apiPositionCreate($request, $response);

            $positionIds[$position['name']]     = $response->get('')['response']->getId();
            $variables['positions'][$key]['id'] = $response->get('')['response']->getId();
            break;
        }
    }
}
//endregion

//region Organization image
if (!\file_exists(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

// upload orange management icon
\copy(__DIR__ . '/img/m_icon.png', __DIR__ . '/temp/m_icon.png');

$module = $app->moduleManager->get('Organization');
TestUtils::setMember($module, 'app', $app);

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->getHeader()->setAccount(1);
$request->setData('name', 'Orange Management Logo');
$request->setData('id', 1);

TestUtils::setMember($request, 'files', [
    'file1' => [
        'name'     => 'm_icon.png',
        'type'     => MimeType::M_PNG,
        'tmp_name' => __DIR__ . '/temp/m_icon.png',
        'error'    => \UPLOAD_ERR_OK,
        'size'     => \filesize(__DIR__ . '/img/m_icon.png'),
    ],
]);
$module->apiUnitImageSet($request, $response);

// upload lima icon
\copy(__DIR__ . '/img/m_icon.png', __DIR__ . '/temp/m_icon.png');

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->getHeader()->setAccount(1);
$request->setData('name', 'Lima Logo');
$request->setData('id', 2);

TestUtils::setMember($request, 'files', [
    'file1' => [
        'name'     => 'm_icon.png',
        'type'     => MimeType::M_PNG,
        'tmp_name' => __DIR__ . '/temp/m_icon.png',
        'error'    => \UPLOAD_ERR_OK,
        'size'     => \filesize(__DIR__ . '/img/m_icon.png'),
    ],
]);
$module->apiUnitImageSet($request, $response);
//endregion
