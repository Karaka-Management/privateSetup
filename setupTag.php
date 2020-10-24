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

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->getHeader()->setAccount(2);

$request->setData('tag', 1);
$request->setData('language', ISO639x1Enum::_DE);
$request->setData('title', 'Beta');
$module->apiTagL11nCreate($request, $response);

$request->setData('tag', 2, true);
$request->setData('title', 'Intranet', true);
$module->apiTagL11nCreate($request, $response);

$request->setData('tag', 3, true);
$request->setData('title', 'Software', true);
$module->apiTagL11nCreate($request, $response);

$request->setData('tag', 4, true);
$request->setData('title', 'FiBu', true);
$module->apiTagL11nCreate($request, $response);
//endregion
