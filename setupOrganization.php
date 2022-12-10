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
 * @link      https://karaka.app
 */
declare(strict_types=1);

use Modules\Admin\Models\GroupMapper;
use Modules\Organization\Models\DepartmentMapper;
use Modules\Organization\Models\Status;
use phpOMS\Account\PermissionOwner;
use phpOMS\Account\PermissionType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\System\MimeType;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;

/**
 * Setup departments
 */
//region Department
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\Organization\Controller\ApiController $module */
$module = $app->moduleManager->get('Organization');
TestUtils::setMember($module, 'app', $app);

$departmentIds = [];

$count    = \count($variables['departments']);
$interval = (int) \ceil($count / 2);
$z        = 0;
$p        = 0;

foreach ($variables['departments'] as $key => $department) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 1;
    $request->setData('name', $department['name']);
    $request->setData('status', Status::ACTIVE);
    $request->setData('unit', 1);
    $request->setData('parent', $departmentIds[$department['parent']] ?? null);
    $request->setData('description', '');

    $module->apiDepartmentCreate($request, $response);
    ++$apiCalls;
    $departmentIds[$department['name']]   = $response->get('')['response']->getId();
    $variables['departments'][$key]['id'] = $response->get('')['response']->getId();

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo $p < 2 ? '░' : '';
//endregion

/**
 * Setup positions
 */
//region Departments
$departments = DepartmentMapper::getAll()->execute();
$postionIds  = [];

$count    = \count($variables['positions']);
$interval = (int) \ceil($count / 6);
$z        = 0;
$p        = 0;

foreach ($variables['positions'] as $key => $position) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 1;
    $request->setData('name', $position['name']);
    $request->setData('status', Status::ACTIVE);
    $request->setData('parent', $positionIds[$position['parent']] ?? null);
    $request->setData('description', '');

    foreach ($departments as $d) {
        if (!isset($position['department']) || $d->name === $position['department']) {
            $request->setData('department', $d->getId());
            $module->apiPositionCreate($request, $response);
            ++$apiCalls;

            $positionIds[$position['name']]     = $response->get('')['response']->getId();
            $variables['positions'][$key]['id'] = $response->get('')['response']->getId();
            break;
        }
    }

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo $p < 6 ? '░' : '';

//endregion

//region Organization image
if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

// upload Jingga icon
\copy(__DIR__ . '/img/m_icon.png', __DIR__ . '/temp/m_icon.png');

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 1;
$request->setData('name', 'Jingga Logo');
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
++$apiCalls;

echo '░';

unset($departments);

$module = $app->moduleManager->get('Admin');
TestUtils::setMember($module, 'app', $app);

$GROUP_LIST = GroupMapper::getAll()->execute();

$groups = [
    ['name' => 'org:dep:management', 'permissions' => [
        [
            'module'               => null,
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 1,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => PermissionType::MODIFY,
            'permissiondelete'     => PermissionType::DELETE,
            'permissionpermission' => PermissionType::PERMISSION,
        ],
        [
            'module'               => null,
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 1,
            'permissionapp'        => 'api',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => PermissionType::MODIFY,
            'permissiondelete'     => PermissionType::DELETE,
            'permissionpermission' => PermissionType::PERMISSION,
        ],
    ]],
];

foreach ($groups as $group) {
    foreach ($GROUP_LIST as $dbGroup) {
        if ($dbGroup->name === $group['name']) {
            foreach ($group['permissions'] as $key => $p) {
                $response = new HttpResponse();
                $request  = new HttpRequest(new HttpUri(''));

                $request->header->account = 1;
                $request->setData('permissionowner', $p['permissionowner']);
                $request->setData('permissionref', $dbGroup->getId());
                $request->setData('permissionunit', $p['permissionunit']);
                $request->setData('permissionapp', $p['permissionapp']);
                $request->setData('permissionmodule', $p['module']);
                $request->setData('permissiontype', $p['permissiontype']);
                $request->setData('permissionelement', $p['permissionelement']);
                $request->setData('permissioncomponent', $p['permissioncomponent']);
                $request->setData('permissioncreate', $p['permissioncreate']);
                $request->setData('permissionread', $p['permissionread']);
                $request->setData('permissionupdate', $p['permissionupdate']);
                $request->setData('permissiondelete', $p['permissiondelete']);
                $request->setData('permissionpermission', $p['permissionpermission']);
                $module->apiAddGroupPermission($request, $response);
                ++$apiCalls;
            }
        }
    }
}

echo '░';
//endregion
