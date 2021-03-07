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
use phpOMS\Utils\IO\Zip\Zip;
use phpOMS\Utils\TestUtils;

/**
 * Create applications
 */
//region Applications
Zip::pack([__DIR__ . '/cms/Demo' => '/'], __DIR__ . '/temp/Demo.zip');

/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\CMS\Controller\ApiController $module */
$module = $app->moduleManager->get('CMS');
TestUtils::setMember($module, 'app', $app);

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 2;
$request->setData('name', 'Demo');

$files = [
    [
        'error'    => \UPLOAD_ERR_OK,
        'type'     => 'zip',
        'name'     => 'Demo.zip',
        'tmp_name' => __DIR__ . '/temp/Demo.zip',
        'size'     => \filesize(__DIR__ . '/temp/Demo.zip'),
    ],
];

TestUtils::setMember($request, 'files', $files);

$module->apiApplicationInstall($request, $response);

if (\is_file(__DIR__ . '/temp/Demo.zip')) {
	\unlink(__DIR__ . '/temp/Demo.zip');
}
//endregion
