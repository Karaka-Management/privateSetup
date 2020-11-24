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

use Modules\QA\Models\QAAnswerStatus;
use Modules\QA\Models\QAQuestionStatus;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;
use phpOMS\Localization\ISO639x1Enum;

/**
 * Create tasks
 *
 * @var \Modules\Kanban\Controller\ApiController $module
 */
//region Kanban
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('QA');
TestUtils::setMember($module, 'app', $app);

$QUESTION_COUNT = 1000;
$CATEGORY_COUNT = 30;
$LOREM_COUNT    = \count(Text::LOREM_IPSUM) - 1;
$LANGUAGES      = \count($variables['languages']);

$categories = [];

$LOREM = \array_slice(Text::LOREM_IPSUM, 0, $CATEGORY_COUNT);
foreach ($LOREM as $j => $word) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = \mt_rand(2, 5);
    $request->setData('language', ISO639x1Enum::_EN);
    $request->setData('name', 'EN:' . $word);

    if ($j > 0 && \mt_rand(1, 100) < 50) {
        $request->setData('parent', $categories[\mt_rand(0, $j - 1)]->getId());
    }

    $module->apiQACategoryCreate($request, $response);
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

        $module->apiQACategoryL11nCreate($request, $response);
    }
}

for ($i = 0; $i < $QUESTION_COUNT; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');

    $request->header->account = \mt_rand(1, 5);
    $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
    $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));
    $request->setData('language', $variables['languages'][\mt_rand(0, $LANGUAGES - 1)]);
    $request->setData('category', $categories[\mt_rand(0, $CATEGORY_COUNT - 1)]->getId());
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

    $module->apiQAQuestionCreate($request, $response);

    $id = $response->get('')['response']->getId();

    //region columns
    $ANSWERS_COUNT  = \mt_rand(-2, 5);
    for ($j = 0; $j < $ANSWERS_COUNT; ++$j) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');

        $request->header->account = \mt_rand(1, 5);
        $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));
        $request->setData('status', QAAnswerStatus::getRandom());
        $request->setData('question', $id);

        $module->apiQAAnswerCreate($request, $response);
    }
    //endregion
}
//endregion
