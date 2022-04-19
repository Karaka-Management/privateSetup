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
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Setup news module
 *
 * @var \Modules\Editor\Controller\ApiController $module
 */
//region Editor
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\Editor\Controller\ApiController $module */
$module = $app->moduleManager->get('Editor');
TestUtils::setMember($module, 'app', $app);

$EDITOR_DOCS = 500;
$LOREM_COUNT = \count(Text::LOREM_IPSUM) - 1;

$count    = $EDITOR_DOCS;
$interval = (int) \ceil($count / 10);
$z        = 0;
$p        = 0;

for ($i = 0; $i < $EDITOR_DOCS; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_5-12');

    $request->header->account = \mt_rand(1, 5);
    $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
    $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));

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

    //region files
    $files = \scandir(__DIR__ . '/media/types');

    $fileCounter = 0;
    $toUpload    = [];
    $mFiles      = [];
    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === 'Video.mp4' || \mt_rand(1, 100) < 76) {
            continue;
        }

        ++$fileCounter;

        if ($fileCounter === 1) {
            \copy(__DIR__ . '/media/types/' . $file, __DIR__ . '/temp/' . $file);

            $toUpload['file' . $fileCounter] = [
                'name'     => $file,
                'type'     => \explode('.', $file)[1],
                'tmp_name' => __DIR__ . '/temp/' . $file,
                'error'    => \UPLOAD_ERR_OK,
                'size'     => \filesize(__DIR__ . '/temp/' . $file),
            ];
        } else {
            $mFiles[] = \mt_rand(1, 9);
        }
    }

    if (!empty($toUpload)) {
        TestUtils::setMember($request, 'files', $toUpload);
    }

    if (!empty($mFiles)) {
        $request->setData('media', \json_encode(\array_unique($mFiles)));
    }
    //endregion

    $module->apiEditorCreate($request, $response);
    ++$apiCalls;

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo \str_repeat('░', 10 - $p);
//endregion
