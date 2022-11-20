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

use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\IO\Zip\Zip;
use phpOMS\Utils\TestUtils;

/**
 * Create applications
 */
//region Applications

/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\CMS\Controller\ApiController $module */
$module = $app->moduleManager->get('CMS');
TestUtils::setMember($module, 'app', $app);

/* Demo Website */
Zip::pack([__DIR__ . '/cms/Demo' => '/'], __DIR__ . '/temp/Demo.zip');

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
++$apiCalls;

if (\is_file(__DIR__ . '/temp/Demo.zip')) {
	\unlink(__DIR__ . '/temp/Demo.zip');
}

echo '░░░░░';

/* OnlineResourceWatcher */
Zip::pack([__DIR__ . '/cms/OnlineResourceWatcher' => '/'], __DIR__ . '/temp/OnlineResourceWatcher.zip');

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 2;
$request->setData('name', 'OnlineResourceWatcher');

$files = [
    [
        'error'    => \UPLOAD_ERR_OK,
        'type'     => 'zip',
        'name'     => 'OnlineResourceWatcher.zip',
        'tmp_name' => __DIR__ . '/temp/OnlineResourceWatcher.zip',
        'size'     => \filesize(__DIR__ . '/temp/OnlineResourceWatcher.zip'),
    ],
];

TestUtils::setMember($request, 'files', $files);

$module->apiApplicationInstall($request, $response);
++$apiCalls;

if (\is_file(__DIR__ . '/temp/OnlineResourceWatcher.zip')) {
    \unlink(__DIR__ . '/temp/OnlineResourceWatcher.zip');
}

echo '░░░░░';
//endregion
