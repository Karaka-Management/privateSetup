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

use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Setup news module
 */
//region OnlineResourceWatcher
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\OnlineResourceWatcher\Controller\ApiController $module */
$module = $app->moduleManager->get('OnlineResourceWatcher');
TestUtils::setMember($module, 'app', $app);

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

$ACCOUNTS  = 10;
$RESOURCES = 50;
$LOREM_COUNT   = \count(Text::LOREM_IPSUM) - 1;

$count    = $ACCOUNTS;
$interval = (int) \ceil($count / 20);
$z        = 0;
$p        = 0;

for ($i = 0; $i < $ACCOUNTS; ++$i) {
    for ($j = \mt_rand(0, $RESOURCES); $j < $RESOURCES; ++$j) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_1-1');

        $request->header->account = \mt_rand(2, 5);
        $request->setData('title', \trim(\strtok($MARKDOWN, "\n"), ' #'));
        $request->setData('path', 'https://jingga.app');

        $module->apiResourceCreate($request, $response);
        ++$apiCalls;
    }

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

for ($i = 0; $i < 5; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $module->apiCheckResources($request, $response);
    ++$apiCalls;

    echo '░';
    ++$p;
}

echo \str_repeat('░', 10 - $p);
//endregion