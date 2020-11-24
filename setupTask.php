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

use Modules\Tasks\Models\TaskPriority;
use Modules\Tasks\Models\TaskStatus;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Create tasks
 *
 * @var \Modules\Tasks\Controller\ApiController $module
 */
//region Tasks
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Tasks');
TestUtils::setMember($module, 'app', $app);

$TASK_COUNT  = 1000;
$LOREM_COUNT = \count(Text::LOREM_IPSUM) - 1;

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

    $module->apiTaskCreate($request, $response);
    $id = $response->get('')['response']->getId();

    //region answers
    $ANSWER_COUNT  = \mt_rand(0, 5);
    for ($j = 0; $j < $ANSWER_COUNT; ++$j) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);
        $request->setData('task', $id);
        $request->setData('status', TaskStatus::getRandom());

        $content = \mt_rand(1, 100);
        if ($content <= 80) {
            $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');
            $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));
        }

        ($DUE_DATE = new \DateTime())->setTimestamp(\time() + \mt_rand(-100000000, 100000000));
        $request->setData('due', $DUE_DATE->format('Y-m-d H:i:s'));
        $request->setData('priority', TaskPriority::getRandom());

        // @todo handle to
        // @todo handle cc

        $module->apiTaskElementCreate($request, $response);
    }
    //endregion
}
//endregion