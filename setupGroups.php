<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Karaka
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use phpOMS\Account\GroupStatus;
use phpOMS\Account\PermissionOwner;
use phpOMS\Account\PermissionType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;

/**
 * Setup groups
 *
 * @var \Modules\Admin\Controller\ApiController @module
 */
//region Groups
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\Admin\Controller\ApiController $module */
$module = $app->moduleManager->get('Admin');
TestUtils::setMember($module, 'app', $app);

$groups = [
    ['name' => 'Management', 'permissions' => []],
    ['name' => 'Executive', 'permissions' => []],
    ['name' => 'R&D', 'permissions' => []],
    ['name' => 'Sales', 'permissions' => []],
    ['name' => 'Service', 'permissions' => []],
    ['name' => 'Support', 'permissions' => []],
    ['name' => 'Secretariat', 'permissions' => [
        [
            'module'               => 'News',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => PermissionType::MODIFY,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'News',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'api',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => PermissionType::MODIFY,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
    ]],
    ['name' => 'HR', 'permissions' => []],
    ['name' => 'Purchasing', 'permissions' => []],
    ['name' => 'QA', 'permissions' => []],
    ['name' => 'QM', 'permissions' => []],
    ['name' => 'IT', 'permissions' => []],
    ['name' => 'Marketing', 'permissions' => []],
    ['name' => 'Warehouse', 'permissions' => []],
    ['name' => 'Registration', 'permissions' => []],
    ['name' => 'Production', 'permissions' => []],
    ['name' => 'Finance', 'permissions' => []],
    ['name' => 'Employee', 'permissions' => [
        [
            'module'               => 'Home',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => 0,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Admin',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'api',
            'permissiontype'       => \Modules\Admin\Models\PermissionState::ACCOUNT_SETTINGS,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => 0,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => PermissionType::MODIFY,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Help',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => 0,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Dashboard',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => 0,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Profile',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => \Modules\Profile\Models\PermissionState::PROFILE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Profile',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'api',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => \Modules\Profile\Models\PermissionState::PROFILE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => PermissionType::MODIFY,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Media',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Media',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'api',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => PermissionType::MODIFY,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'News',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => 0,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Tasks',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Tasks',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'api',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Calendar',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Calendar',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'api',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'MyPrivate',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => 0,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Helper',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => 0,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Knowledgebase',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => 0,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'HumanResourceTimeRecording',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => \Modules\HumanResourceTimeRecording\Models\PermissionState::PRIVATE_DASHBOARD,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => 0,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'HumanResourceTimeRecording',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'api',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => 0,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Editor',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => PermissionType::MODIFY,
            'permissiondelete'     => PermissionType::DELETE,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Editor',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'api',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => PermissionType::MODIFY,
            'permissiondelete'     => PermissionType::DELETE,
            'permissionpermission' => 0,
        ],
    ]],
    ['name' => 'Controlling', 'permissions' => [
        [
            'module'               => 'Helper',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => PermissionType::MODIFY,
            'permissiondelete'     => 16,
            'permissionpermission' => 32,
        ],
        [
            'module'               => 'Helper',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'api',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => PermissionType::MODIFY,
            'permissiondelete'     => 16,
            'permissionpermission' => 32,
        ],
        [
            'module'               => 'Tag',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'backend',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => PermissionType::MODIFY,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
        [
            'module'               => 'Tag',
            'permissionowner'      => PermissionOwner::GROUP,
            'permissionunit'       => 2,
            'permissionapp'        => 'api',
            'permissiontype'       => null,
            'permissionelement'    => null,
            'permissioncomponent'  => null,
            'permissioncreate'     => PermissionType::CREATE,
            'permissionread'       => PermissionType::READ,
            'permissionupdate'     => PermissionType::MODIFY,
            'permissiondelete'     => 0,
            'permissionpermission' => 0,
        ],
    ]],
];

foreach ($groups as $group) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 1;
    $request->setData('name', $group['name']);
    $request->setData('status', GroupStatus::ACTIVE);
    $module->apiGroupCreate($request, $response);
    ++$apiCalls;

    if (!empty($group['permissions'])) {
        $g = $response->get('')['response'];
        foreach ($group['permissions'] as $key => $p) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = 1;
            $request->setData('permissionowner', $p['permissionowner']);
            $request->setData('permissionref', $g->getId());
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

echo '░░░░░░░░░░';
//endregion
