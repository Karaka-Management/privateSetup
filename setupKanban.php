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
 *
 * @var \Modules\Kanban\Controller\ApiController $module
 */
//region Kanban
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Kanban');
TestUtils::setMember($module, 'app', $app);

$KANBAN_COUNT = 100;
$LOREM_COUNT  = \count(Text::LOREM_IPSUM) - 1;

for ($i = 0; $i < $KANBAN_COUNT; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');

    $request->header->account = \mt_rand(1, 5);
    $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
    $request->setData('status', BoardStatus::getRandom());

    $module->apiKanbanBoardCreate($request, $response);

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

        $columnId = $response->get('')['response']->getId();

        //region cards
        $CARD_COUNT  = \mt_rand(0, 50);
        for ($k = 0; $k < $CARD_COUNT; ++$k) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');

            $request->header->account = \mt_rand(2, 5);
            $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
            $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));
            $request->setData('type', CardType::getRandom());
            $request->setData('status', CardStatus::getRandom());
            $request->setData('order', $k + 1);
            $request->setData('column', $columnId);

            $module->apiKanbanCardCreate($request, $response);

            $cardId = $response->get('')['response']->getId();

            $COMMENT_COUNT  = \mt_rand(0, 5);
            for ($l = 0; $l < $COMMENT_COUNT; ++$l) {
                $response = new HttpResponse();
                $request  = new HttpRequest(new HttpUri(''));

                $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');

                $request->header->account = \mt_rand(2, 5);
                $request->setData('plain', \preg_replace('/^.+\n/', '', $MARKDOWN));
                $request->setData('card', $cardId);

                $module->apiKanbanCardCommentCreate($request, $response);
            }
        }
        //endregion
    }
    //endregion
}
//endregion