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
