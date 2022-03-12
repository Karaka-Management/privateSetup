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

use Modules\Tasks\Models\TaskPriority;
use Modules\Tasks\Models\TaskStatus;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Create tasks
 */
//region Tasks
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\Tasks\Controller\ApiController $module */
$module = $app->moduleManager->get('Tasks');
TestUtils::setMember($module, 'app', $app);

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

$TASK_COUNT  = 250;
$LOREM_COUNT = \count(Text::LOREM_IPSUM) - 1;

$count    = $TASK_COUNT;
$interval = (int) \ceil($count / 10);
$z        = 0;
$p        = 0;

for ($i = 0; $i < $TASK_COUNT; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');

    $request->header->account = \mt_rand(1, 5);
    $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
    $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));
    $request->setData('forward', \mt_rand(2, 5));

    ($DUE_DATE = new \DateTime())->setTimestamp(\time() + \mt_rand(-100000000, 100000000));
    $request->setData('due', $DUE_DATE->format('Y-m-d H:i:s'));
    $request->setData('priority', TaskPriority::getRandom());

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
        if ($file === '.' || $file === '..' || $file === 'Video.mp4' || \mt_rand(1, 100) < 81) {
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

    $module->apiTaskCreate($request, $response);
    ++$apiCalls;
    $id = $response->get('')['response']->getId();

    $completion = 0;

    //region answers
    $ANSWER_COUNT  = \mt_rand(0, 3);
    for ($j = 0; $j < $ANSWER_COUNT; ++$j) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);
        $request->setData('task', $id);
        $request->setData('status', TaskStatus::getRandom());

        $content = \mt_rand(1, 100);
        if ($content <= 80) {
            $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_1-1');
            $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));

            //region files
            $files = \scandir(__DIR__ . '/media/types');

            $fileCounter = 0;
            $toUpload    = [];
            $mFiles      = [];
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || $file === 'Video.mp4' || \mt_rand(1, 100) < 91) {
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
        }

        ($DUE_DATE = new \DateTime())->setTimestamp(\time() + \mt_rand(-100000000, 100000000));
        $request->setData('due', $DUE_DATE->format('Y-m-d H:i:s'));
        $request->setData('priority', TaskPriority::getRandom());

        if (\mt_rand(0, 100) < 21) {
            $request->setData('completion', $completion = \mt_rand($completion, 100));
        }

        // @todo handle to
        // @todo handle cc

        $module->apiTaskElementCreate($request, $response);
        ++$apiCalls;
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
