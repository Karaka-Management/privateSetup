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
 * Setup Helper module
 *
 * @var \Modules\Media\Controller\ApiController $module
 */
//region Helper
/** @var \Modules\Helper\Controller\ApiController $module */
$module = $app->moduleManager->get('Helper');
TestUtils::setMember($module, 'app', $app);

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

$helpers = \scandir(__DIR__ . '/helper');

$count = \count($helpers);
$interval = (int) \ceil($count / 10);
$z = 0;
$p = 0;

foreach ($helpers as $helper) {
    if (!\is_dir(__DIR__ . '/helper/' . $helper) || $helper === '..' || $helper === '.') {
        ++$z;
        if ($z % $interval === 0) {
            echo '░';
            ++$p;
        }

        continue;
    }

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 2;
    $request->setData('name', \ucfirst($helper));
    $request->setData('standalone', true);

    $files = [];

    $helperFiles = \scandir(__DIR__ . '/helper/' . $helper);
    foreach ($helperFiles as $filePath) {
        if (!\is_file(__DIR__ . '/helper/' . $helper . '/' . $filePath) || $filePath === '..' || $filePath === '.') {
            continue;
        }

        \copy(__DIR__ . '/helper/' . $helper . '/' . $filePath, __DIR__ . '/temp/' . $filePath);

        $files[] = [
            'error'    => \UPLOAD_ERR_OK,
            'type'     => \substr($filePath, \strrpos($filePath, '.') + 1),
            'name'     => $filePath,
            'tmp_name' => __DIR__ . '/temp/' . $filePath,
            'size'     => \filesize(__DIR__ . '/temp/' . $filePath),
        ];
    }

    TestUtils::setMember($request, 'files', $files);

    // tags
    $tags      = [];
    $TAG_COUNT = \mt_rand(1, 3);
    $added     = [];

    for ($j = 0; $j < $TAG_COUNT; ++$j) {
        $tagId = \mt_rand(1, $LOREM_COUNT - 1);

        if (!\in_array($tagId, $added)) {
            $added[] = $tagId;
            $tags[]  = ['id' => $tagId];
        }
    }

    if (!empty($tags)) {
        $request->setData('tags', \json_encode($tags));
    }

    $module->apiTemplateCreate($request, $response);

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo \str_repeat('░', 10 - $p);
//endregion
