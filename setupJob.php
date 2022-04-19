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
 * Setup Job module
 *
 * @var \Modules\Job\Controller\ApiController $module
 */
//region Job
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\Job\Controller\ApiController $module */
$module = $app->moduleManager->get('Job');
TestUtils::setMember($module, 'app', $app);

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 1;
$request->setData('title', 'Error Reporter');
$request->setData('description', 'Send daily log file if errors exist.');
$request->setData(
    'cmd_linux',
    '0 * * * * php /home/spl1nes/Orange-Management/cli.php put:/admin/monitoring/log -t email >/dev/null 2>&1'
);
$request->setData(
    'cmd_win',
    'schtasks /create /sc hourly /tn Error Reporter /tr php /home/spl1nes/Orange-Management/cli.php put:/admin/monitoring/log -t email'
);

$module->apiJobCreate($request, $response);
++$apiCalls;

echo '░░░░░░░░░░';
//endregion
