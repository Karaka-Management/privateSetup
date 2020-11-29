<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   OrangeManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
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
 *
 * @var \Modules\News\Controller\ApiController $module
 */
//region News
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('News');
TestUtils::setMember($module, 'app', $app);

$NEWS_ARTICLES = 100;
$FEATURED_PROB = 10;
$LOREM_COUNT   = \count(Text::LOREM_IPSUM) - 1;

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

        $module->apiNewsCreate($request, $response);

        //region comments
        $COMMENT_COUNT = \mt_rand(0, 20);
        $commentModule = $app->moduleManager->get('Comments');
        $commentList   = $response->get('')['response']->comments->getId();

        for ($j = 0; $j < $COMMENT_COUNT; ++$j) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');

            $request->header->account = \mt_rand(2, 5);
            $request->setData('list', $commentList);
            $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
            $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));

            $ref = \mt_rand(-20, $j);
            if ($ref > 0) {
                $request->setData('ref', $ref);
            }

            $commentModule->apiCommentCreate($request, $response);
        }
        //endregion
    }
}
//endregion
