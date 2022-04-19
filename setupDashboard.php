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
use phpOMS\Utils\TestUtils;

/**
 * Setup dashboard module
 *
 * @var \Modules\Dashboard\Controller\ApiController $module
 */
//region Dashboard
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\Dashboard\Controller\ApiController $module */
$module = $app->moduleManager->get('Dashboard');
TestUtils::setMember($module, 'app', $app);

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 1;

echo '░░░░░░░░░░';
//endregion
