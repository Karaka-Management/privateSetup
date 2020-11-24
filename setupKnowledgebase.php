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

use Modules\Knowledgebase\Models\WikiAppMapper;
use Modules\Knowledgebase\Models\WikiStatus;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Setup news module
 *
 * @var \Modules\Knowledgebase\Controller\ApiController $module
 */
//region Knowledgebase
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Knowledgebase');
TestUtils::setMember($module, 'app', $app);

$WIKI_ARTICLES = 500;
$APPS          = WikiAppMapper::count();
$LOREM_COUNT   = \count(Text::LOREM_IPSUM) - 1;

for ($i = 0; $i < 1; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 1;
    $request->setData('name', Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)]);

    $module->apiWikiAppCreate($request, $response);
}

for ($i = 0; $i < $APPS + 1; ++$i) {
    $categories = [];
    $j          = 0;

    foreach (Text::LOREM_IPSUM as $word) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(1, 5);
        $request->setData('name', 'EN:' . $word);
        $request->setData('app', \mt_rand(1, $APPS));

        if ($j > 0 && \mt_rand(1, 100) < 50) {
            $request->setData('parent', $categories[\mt_rand(0, $j - 1)]->getId());
        }

        $module->apiWikiCategoryCreate($request, $response);
        $category     = $response->get('')['response'];
        $categories[] = $category;

        foreach ($variables['languages'] as $language) {
            if ($language === ISO639x1Enum::_EN) {
                continue;
            }

            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = \mt_rand(2, 5);

            $request->setData('category', $category->getId());
            $request->setData('language', $language);
            $request->setData('name', \strtoupper($language) . ':' . Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)]);

            $module->apiWikiCategoryL11nCreate($request, $response);
        }

        ++$j;
    }
}

foreach ($variables['languages'] as $language) {
    for ($i = 0; $i < $WIKI_ARTICLES; ++$i) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_5-12');

        $request->header->account = \mt_rand(1, 5);
        $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
        $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));
        $request->setData('language', $language);
        $request->setData('status', WikiStatus::ACTIVE);
        $request->setData('app', \mt_rand(1, $APPS));

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

        $module->apiWikiDocCreate($request, $response);
    }
}
//endregion
