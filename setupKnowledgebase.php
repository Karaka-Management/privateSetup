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

use Modules\Knowledgebase\Models\WikiStatus;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;

/**
 * Setup knowledgebase module
 *
 * @var \Modules\Knowledgebase\Controller\ApiController $module
 */
//region Knowledgebase
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Knowledgebase');
TestUtils::setMember($module, 'app', $app);

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->getHeader()->setAccount(2);
$request->setData('title', 'Finance');
$request->setData('status', WikiStatus::ACTIVE);
$request->setData('lang', ISO639x1Enum::_DE);
$request->setData('path', '/Finance');
$request->setData('parent', 1);
$module->apiWikiCategoryCreate($request, $response);

$request->getHeader()->setAccount(2);
$request->setData('title', 'Monatsabschluss: Monatsfortschreibung', true);
$request->setData('plain', \file_get_contents(__DIR__ . '/knowledgebase/wiki_fibu_closing_month.md'));
$request->setData('status', WikiStatus::ACTIVE);
$request->setData('category', 2);
$request->setData('language', ISO639x1Enum::_DE);

$tags = [
    [
        'title'    => 'Software',
        'language' => ISO639x1Enum::_EN,
        'color'    => '#ff0000ff',
    ],
    [
        'title'    => 'FiBu',
        'language' => ISO639x1Enum::_EN,
        'color'    => '#ff0000ff',
    ],
];

$request->setData('tags', \json_encode($tags));
$module->apiWikiDocCreate($request, $response);
//endregion
