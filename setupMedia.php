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

use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;

/**
 * Create tasks
 */
//region Media
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\Media\Controller\ApiController $module */
$module = $app->moduleManager->get('Media');
TestUtils::setMember($module, 'app', $app);

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

$files = \scandir(__DIR__ . '/media/types');

$count    = \count($files);
$interval = (int) \ceil($count / 10);
$z        = 0;
$p        = 0;

foreach ($files as $file) {
    if ($file === '.' || $file === '..') {
        ++$z;
        if ($z % $interval === 0) {
            echo '░';
            ++$p;
        }

        continue;
    }

    \copy(__DIR__ . '/media/types/' . $file, __DIR__ . '/temp/' . $file);

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = \mt_rand(2, 5);
    $request->setData('virtualpath', '/');

    // tags
    $tags      = [];
    $TAG_COUNT = \mt_rand(0, 4);
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
    ++$apiCalls;
    $variables['mFiles'] = \array_merge($variables['mFiles'], $response->get('')['response']);

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo \str_repeat('░', 10 - $p);
//endregion
