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

use Modules\QA\Models\QAAnswerStatus;
use Modules\QA\Models\QAQuestionStatus;
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
/** @var \Modules\QA\Controller\ApiController $module */
$module = $app->moduleManager->get('QA');
TestUtils::setMember($module, 'app', $app);

$QUESTION_COUNT = 500;
$LOREM_COUNT    = \count(Text::LOREM_IPSUM) - 1;
$LANGUAGES      = \count($variables['languages']);

// create public QA board

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = \mt_rand(2, 5);
$request->setData('name', Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT - 1)]);

$module->apiQAAppCreate($request, $response);
++$apiCalls;

echo '░';

$count    = $QUESTION_COUNT;
$interval = (int) \ceil($count / 9);
$z        = 0;
$p        = 0;

for ($i = 0; $i < $QUESTION_COUNT; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');

    $request->header->account = \mt_rand(1, 5);
    $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
    $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));
    $request->setData('language', $variables['languages'][\mt_rand(0, $LANGUAGES - 1)]);
    $request->setData('app', \mt_rand(1, 2));
    $request->setData('status', QAQuestionStatus::getRandom());

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

    $module->apiQAQuestionCreate($request, $response);
    ++$apiCalls;

    $qId = $response->get('')['response']->getId();

    $qVotes   = \mt_rand(-2, 5);
    $sign     = $qVotes <=> 0;
    $maxVotes = \abs($qVotes);

    for ($k = 0; $k < $maxVotes; ++$k) {
        $response                 = new HttpResponse();
        $request                  = new HttpRequest(new HttpUri(''));
        $request->header->account = \mt_rand(1, 5);

        $request->setData('id', $qId);
        $request->setData('type', $sign * 1);
        $module->apiChangeQAQuestionVote($request, $response);
        ++$apiCalls;
    }

    //region columns
    $ANSWERS_COUNT  = \mt_rand(-2, 5);
    $isAccepted     = false;
    for ($j = 0; $j < $ANSWERS_COUNT; ++$j) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');

        $request->header->account = \mt_rand(1, 5);
        $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));
        $request->setData('status', QAAnswerStatus::getRandom());
        $request->setData('question', $qId);

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

        $module->apiQAAnswerCreate($request, $response);
        ++$apiCalls;
        $aId = $response->get('')['response']->getId();

        $aVotes   = \mt_rand(-2, 5);
        $sign     = $aVotes <=> 0;
        $maxVotes = \abs($aVotes);

        for ($k = 0; $k < $maxVotes; ++$k) {
            $response                 = new HttpResponse();
            $request                  = new HttpRequest(new HttpUri(''));
            $request->header->account = \mt_rand(1, 5);

            $request->setData('id', $aId);
            $request->setData('type', $sign * 1);
            $module->apiChangeQAAnswerVote($request, $response);
            ++$apiCalls;
        }

        if (!$isAccepted && ($isAccepted = \mt_rand(1, 100) < 10)) {
            $response                 = new HttpResponse();
            $request                  = new HttpRequest(new HttpUri(''));
            $request->header->account = \mt_rand(1, 5);

            $request->setData('id', $aId);
            $request->setData('accepted', '1');
            $module->apiChangeAnsweredStatus($request, $response);
            ++$apiCalls;
        }
    }
    //endregion

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo \str_repeat('░', 9 - $p);
//endregion
