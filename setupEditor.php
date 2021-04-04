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
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Setup news module
 *
 * @var \Modules\Editor\Controller\ApiController $module
 */
//region Editor
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Editor');
TestUtils::setMember($module, 'app', $app);

$EDITOR_DOCS = 1000;
$LOREM_COUNT = \count(Text::LOREM_IPSUM) - 1;

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

    $module->apiEditorCreate($request, $response);

    $docId = $response->get('')['response']->getId();

    //region client files
    $files = \scandir(__DIR__ . '/media/types');

    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || \mt_rand(1, 100) < 90) {
            continue;
        }

        \copy(__DIR__ . '/media/types/' . $file, __DIR__ . '/temp/' . $file);

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);
        $request->setData('doc', $docId);

        TestUtils::setMember($request, 'files', [
            'file1' => [
                'name'     => $file,
                'type'     => \explode('.', $file)[1],
                'tmp_name' => __DIR__ . '/temp/' . $file,
                'error'    => \UPLOAD_ERR_OK,
                'size'     => \filesize(__DIR__ . '/temp/' . $file),
            ],
        ]);

        $module->apiFileCreate($request, $response);
    }
    //endregion
}
//endregion
