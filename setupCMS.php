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

/* Frontend */
Zip::pack([__DIR__ . '/cms/Frontend' => '/'], __DIR__ . '/temp/Frontend.zip');

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 1;
$request->setData('name', 'Frontend');

$files = [
    [
        'error'    => \UPLOAD_ERR_OK,
        'type'     => 'zip',
        'name'     => 'Frontend.zip',
        'tmp_name' => __DIR__ . '/temp/Frontend.zip',
        'size'     => \filesize(__DIR__ . '/temp/Frontend.zip'),
    ],
];

TestUtils::setMember($request, 'files', $files);

$module->apiApplicationInstall($request, $response);
++$apiCalls;

if (\is_file(__DIR__ . '/temp/Frontend.zip')) {
    \unlink(__DIR__ . '/temp/Frontend.zip');
}

echo '░░░░░';

/* ORW */
Zip::pack([__DIR__ . '/cms/Orw' => '/'], __DIR__ . '/temp/Orw.zip');

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 1;
$request->setData('name', 'Orw');

$files = [
    [
        'error'    => \UPLOAD_ERR_OK,
        'type'     => 'zip',
        'name'     => 'Orw.zip',
        'tmp_name' => __DIR__ . '/temp/Orw.zip',
        'size'     => \filesize(__DIR__ . '/temp/Orw.zip'),
    ],
];

TestUtils::setMember($request, 'files', $files);

$module->apiApplicationInstall($request, $response);
++$apiCalls;

if (\is_file(__DIR__ . '/temp/Orw.zip')) {
    \unlink(__DIR__ . '/temp/Orw.zip');
}

echo '░░░░░';
//endregion

$module = $app->moduleManager->get('Admin');
TestUtils::setMember($module, 'app', $app);

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 1;
$request->setData('settings', \json_encode([
    ['path' => 'app/default/app', 'value' => 'Frontend'],
    ['path' => 'app/default/id', 'value' => 'frontend'],
    ['path' => 'app/domains/127.0.0.1/app', 'value' => 'Frontend'],
    ['path' => 'app/domains/127.0.0.1/id', 'value' => 'frontend'],
]));

$module->apiAppConfigSet($request, $response);
++$apiCalls;
