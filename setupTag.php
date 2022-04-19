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

use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Create tags and their localization
 */
//region Tag
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\Tag\Controller\ApiController $module */
$module = $app->moduleManager->get('Tag');
TestUtils::setMember($module, 'app', $app);

$tagIcons = [
    'fa fa-cogs', 'fa fa-address-book-o', 'fa fa-archive', 'fa fa-balance-scale', 'fa fa-bank', 'fa fa-car', 'fa fa-bell-o', 'fa fa-bolt', 'fa fa-book', 'fa fa-briefcase', 'fa fa-bullseye', 'fa fa-calendar', 'fa fa-bug', 'fa fa-bullhorn', 'fa fa-code', 'fa fa-comment-o', 'fa fa-credit-card', 'fa fa-envelope-o', 'fa fa-desktop', 'fa fa-flask', 'fa fa-heart-o', 'fa fa-home', 'fa fa-photo', 'fa fa-plane', 'fa fa-quote-right', 'fa fa-question-circle-o', 'fa fa-tag', 'fa fa-star-o', 'fa fa-wrench', 'fa fa-user-o', 'fa fa-file-o', 'fa fa-pencil', 'fa fa-paw', 'fa fa-shopping-cart', 'fa fa-sitemap', 'fa fa-info-circle', 'fa fa-life-ring', 'fa fa-gift', 'fa fa-glass', 'fa fa-bed', 'fa fa-cloud', 'fa fa-clock-o', 'fa fa-diamond',
];

$LOREM_COUNT = \count(Text::LOREM_IPSUM) - 1;
$COLOR_COUNT = \count($variables['colors']) - 1;
$ICON_COUNT  = \count($tagIcons) - 1;

$count    = \count(Text::LOREM_IPSUM);
$interval = (int) \ceil($count / 10);
$z        = 0;
$p        = 0;

foreach (Text::LOREM_IPSUM as $word) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = \mt_rand(2, 5);

    $request->setData('language', ISO639x1Enum::_EN);
    $request->setData('title', 'EN:' . $word);
    $request->setData('color', $variables['colors'][\mt_rand(0, $COLOR_COUNT)]);

    if (\mt_rand(1, 100) < 51) {
        $request->setData('icon', $tagIcons[\mt_rand(0, $ICON_COUNT)]);
    }

    $module->apiTagCreate($request, $response);
    ++$apiCalls;

    $id = $response->get('')['response']->getId();
    foreach ($variables['languages'] as $language) {
        if ($language === ISO639x1Enum::_EN) {
            continue;
        }

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);

        $request->setData('tag', $id);
        $request->setData('language', $language);
        $request->setData('title', \strtoupper($language) . ':' . Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)]);

        $module->apiTagL11nCreate($request, $response);
        ++$apiCalls;
    }

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo \str_repeat('░', 10 - $p);
//endregion
