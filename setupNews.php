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
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
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

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->getHeader()->setAccount(2);
$request->setData('publish', 'now');
$request->setData('title', 'Welcome beta testers!');
$request->setData('plain', \file_get_contents(__DIR__ . '/news/news_welcome.en.md'));
$request->setData('type', NewsType::ARTICLE);
$request->setData('status', NewsStatus::VISIBLE);
$request->setData('featured', true);
$request->setData('allow_comments', 1);
$request->setData('lang', ISO639x1Enum::_EN);

$tags = [
    [
        'title'    => 'Beta',
        'language' => ISO639x1Enum::_EN,
        'color'    => '#ff0000ff',
    ],
    [
        'title'    => 'Intranet',
        'language' => ISO639x1Enum::_EN,
        'color'    => '#ff0000ff',
    ],
];

$request->setData('tags', \json_encode($tags));

$module->apiNewsCreate($request, $response);

$request->setData('title', 'Willkommen Betatester!', true);
$request->setData('plain', \file_get_contents(__DIR__ . '/news/news_welcome.de.md'), true);
$request->setData('lang', ISO639x1Enum::_DE, true);
$request->setData('tags', \json_encode([['id' => 1], ['id' => 2]]), true);

$module->apiNewsCreate($request, $response);
//endregion
