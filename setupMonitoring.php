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

echo '░░░░░░░░░░';

//endregion
