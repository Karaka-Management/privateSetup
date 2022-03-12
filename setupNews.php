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

use Modules\News\Models\NewsStatus;
use Modules\News\Models\NewsType;
use phpOMS\Localization\ISO639Enum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Setup news module
 */
//region News
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\News\Controller\ApiController $module */
$module = $app->moduleManager->get('News');
TestUtils::setMember($module, 'app', $app);

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

$NEWS_ARTICLES = 50;
$FEATURED_PROB = 10;
$LOREM_COUNT   = \count(Text::LOREM_IPSUM) - 1;

$count    = \count($variables['languages']);
$interval = (int) \ceil($count / 10);
$z        = 0;
$p        = 0;

foreach ($variables['languages'] as $language) {
    for ($i = 0; $i < $NEWS_ARTICLES; ++$i) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        ($PUBLISH_DATE = new \DateTime())->setTimestamp(\time() + \mt_rand(-100000000, 100000000));
        $MARKDOWN      = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_5-12');

        $request->header->account = \mt_rand(2, 5);
        $request->setData('publish', $PUBLISH_DATE->format('Y-m-d H:i:s'));
        $request->setData('title', ISO639Enum::getByName('_' . \strtoupper($language)) . ': ' . \trim(\strtok($MARKDOWN, "\n"), ' #'));
        $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));
        $request->setData('type', NewsType::ARTICLE);
        $request->setData('status', NewsStatus::VISIBLE);
        $request->setData('featured', \mt_rand(1, 100) <= $FEATURED_PROB ? true : false);
        $request->setData('allow_comments', 1);
        $request->setData('lang', $language);

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

        $module->apiNewsCreate($request, $response);
        ++$apiCalls;

        //region comments
        $COMMENT_COUNT = \mt_rand(0, 7);
        /** @var \Modules\Comments\Controller\ApiController $commentModule */
        $commentModule = $app->moduleManager->get('Comments');
        $commentList   = $response->get('')['response']->comments->getId();

        for ($j = 0; $j < $COMMENT_COUNT; ++$j) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_1-1');

            $request->header->account = \mt_rand(2, 5);
            $request->setData('list', $commentList);
            $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
            $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));

            $ref = \mt_rand(-20, $j);
            if ($ref > 0) {
                $request->setData('ref', $ref);
            }

            //region files
            $files = \scandir(__DIR__ . '/media/types');

            $fileCounter = 0;
            $toUpload    = [];
            $mFiles      = [];
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || $file === 'Video.mp4' || \mt_rand(1, 100) < 98) {
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

            $commentModule->apiCommentCreate($request, $response);
            ++$apiCalls;
        }
        //endregion
    }

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo \str_repeat('░', 10 - $p);
//endregion
