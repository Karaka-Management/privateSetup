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

use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Create tags and their localization
 *
 * @var \Modules\Tag\Controller\ApiController $module
 */
//region Tag
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Tag');
TestUtils::setMember($module, 'app', $app);

$LOREM_COUNT = count(Text::LOREM_IPSUM) - 1;
$COLOR_COUNT = count($variables['colors']) - 1;

foreach (Text::LOREM_IPSUM as $word) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->getHeader()->setAccount(\mt_rand(2, 5));

    $request->setData('language', ISO639x1Enum::_EN);
    $request->setData('title', 'EN:' . $word);
    $request->setData('color', $variables['colors'][\mt_rand(0, $COLOR_COUNT)]);

    $module->apiTagCreate($request, $response);

    $id = $response->get('')['response']->getId();
    foreach ($variables['languages'] as $language) {
        if ($language === ISO639x1Enum::_EN) {
            continue;
        }

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->getHeader()->setAccount(\mt_rand(2, 5));

        $request->setData('tag', $id);
        $request->setData('language', $language);
        $request->setData('title', \strtoupper($language) . ':' . Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)]);

        $module->apiTagL11nCreate($request, $response);
    }
}
//endregion
