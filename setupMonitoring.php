<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   OrangeManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Create tasks
 *
 * @var \Modules\Monitoring\Controller\ApiController $module
 */
//region Monitoring
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Monitoring');
TestUtils::setMember($module, 'app', $app);

//endregion
