<?php
/**
 * Karaka
 *
 * PHP Version 8.0
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
 * Setup news module
 *
 * @var \Modules\Exchange\Controller\ApiController $module
 */
//region Exchange
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\Exchange\Controller\ApiController $module */
$module = $app->moduleManager->get('Exchange');
TestUtils::setMember($module, 'app', $app);

// Install exchange interfaces
$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 1;
$request->setData('interface', 'OMS');

$module->apiInterfaceInstall($request, $response);
++$apiCalls;

echo '░░░░░░░░░░';
//endregion
