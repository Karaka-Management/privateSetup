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

use Modules\Kanban\Models\BoardStatus;
use Modules\Kanban\Models\CardStatus;
use Modules\Kanban\Models\CardType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Create tasks
 */
//region Kanban
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\Kanban\Controller\ApiController $module */
$module = $app->moduleManager->get('Kanban');
TestUtils::setMember($module, 'app', $app);

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

$KANBAN_COUNT = 30;
$LOREM_COUNT  = \count(Text::LOREM_IPSUM) - 1;

$count    = $KANBAN_COUNT - 1;
$interval = (int) \ceil($count / 10);
$z        = 0;
$p        = 0;

for ($i = 0; $i < $KANBAN_COUNT; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_1-1');

    $request->header->account = \mt_rand(1, 5);
    $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
    $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));
    $request->setData('status', BoardStatus::getRandom());

    // tags
    $tags      = [];
    $TAG_COUNT = \mt_rand(0, 3);
    $added     = [];

    for ($c = 0; $c < $TAG_COUNT; ++$c) {
        $tagId = \mt_rand(1, $LOREM_COUNT - 1);

        if (!\in_array($tagId, $added)) {
            $added[] = $tagId;
            $tags[]  = ['id' => $tagId];
        }
    }

    if (!empty($tags)) {
        $request->setData('tags', \json_encode($tags));
    }

    $module->apiKanbanBoardCreate($request, $response);
    ++$apiCalls;

    $boardId = $response->get('')['response']->getId();

    //region columns
    $COLUMN_COUNT  = \mt_rand(3, 5);
    for ($j = 0; $j < $COLUMN_COUNT; ++$j) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);
        $request->setData('title', Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)]);
        $request->setData('order', $j + 1);
        $request->setData('board', $boardId);

        $module->apiKanbanColumnCreate($request, $response);
        ++$apiCalls;

        $columnId = $response->get('')['response']->getId();

        //region cards
        $CARD_COUNT  = \mt_rand(0, 10);
        for ($k = 0; $k < $CARD_COUNT; ++$k) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_1-1');

            $request->header->account = \mt_rand(2, 5);
            $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
            $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));
            $request->setData('type', CardType::getRandom());
            $request->setData('status', CardStatus::getRandom());
            $request->setData('order', $k + 1);
            $request->setData('column', $columnId);

            // tags
            $tags      = [];
            $TAG_COUNT = \mt_rand(0, 3);
            $added     = [];

            for ($c = 0; $c < $TAG_COUNT; ++$c) {
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
                if ($file === '.' || $file === '..' || $file === 'Video.mp4' || \mt_rand(1, 100) < 86) {
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

            $module->apiKanbanCardCreate($request, $response);
            ++$apiCalls;

            $cardId = $response->get('')['response']->getId();

            $COMMENT_COUNT  = \mt_rand(0, 3);
            for ($l = 0; $l < $COMMENT_COUNT; ++$l) {
                $response = new HttpResponse();
                $request  = new HttpRequest(new HttpUri(''));

                $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_1-1');

                $request->header->account = \mt_rand(2, 5);
                $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));
                $request->setData('card', $cardId);

                //region files
                $files = \scandir(__DIR__ . '/media/types');

                $fileCounter = 0;
                $toUpload    = [];
                $mFiles      = [];
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..' || $file === 'Video.mp4' || \mt_rand(1, 100) < 96) {
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

                $module->apiKanbanCardCommentCreate($request, $response);
                ++$apiCalls;
            }
        }
        //endregion
    }
    //endregion

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo \str_repeat('░', 10 - $p);
//endregion
