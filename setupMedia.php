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
use phpOMS\Utils\TestUtils;

/**
 * Create tasks
 *
 * @var \Modules\Media\Controller\ApiController $module
 */
//region Media
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Media');
TestUtils::setMember($module, 'app', $app);

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

$files = \scandir(__DIR__ . '/media/types');

foreach ($files as $file) {
    if ($file === '.' || $file === '..') {
        continue;
    }

    \copy(__DIR__ . '/media/types/' . $file, __DIR__ . '/temp/' . $file);

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = \mt_rand(2, 5);
    $request->setData('virtualpath', '/');

    TestUtils::setMember($request, 'files', [
        [
            'name'     => $file,
            'type'     => \explode('.', $file)[1],
            'tmp_name' => __DIR__ . '/temp/' . $file,
            'error'    => \UPLOAD_ERR_OK,
            'size'     => \filesize(__DIR__ . '/temp/' . $file),
        ],
    ]);

    $module->apiMediaUpload($request, $response);
}
//endregion