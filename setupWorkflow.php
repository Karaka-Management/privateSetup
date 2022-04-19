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
use phpOMS\Utils\RnG\Text;

/**
 * Setup Workflow module
 *
 * @var \Modules\Media\Controller\ApiController $module
 */
//region Workflow
/** @var \Modules\Workflow\Controller\ApiController $module */
$module = $app->moduleManager->get('Workflow');
TestUtils::setMember($module, 'app', $app);

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

$LOREM_COUNT = \count(Text::LOREM_IPSUM) - 1;

$workflows = \scandir(__DIR__ . '/workflow');

$count    = \count($workflows);
$interval = (int) \ceil($count / 10);
$z        = 0;
$p        = 0;

foreach ($workflows as $workflow) {
    if (!\is_dir(__DIR__ . '/workflow/' . $workflow) || $workflow === '..' || $workflow === '.') {
        ++$z;
        if ($z % $interval === 0) {
            echo '░';
            ++$p;
        }

        continue;
    }

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 2;
    $request->setData('name', \str_replace('_', ' ', \ucfirst($workflow)));
    $request->setData('', '');

    $files = [];

    $workflowFiles = \scandir(__DIR__ . '/workflow/' . $workflow);
    foreach ($workflowFiles as $filePath) {
        if (!\is_file(__DIR__ . '/workflow/' . $workflow . '/' . $filePath) || $filePath === '..' || $filePath === '.') {
            continue;
        }

        \copy(__DIR__ . '/workflow/' . $workflow . '/' . $filePath, __DIR__ . '/temp/' . $filePath);

        $files[] = [
            'error'    => \UPLOAD_ERR_OK,
            'type'     => \substr($filePath, \strrpos($filePath, '.') + 1),
            'name'     => $filePath,
            'tmp_name' => __DIR__ . '/temp/' . $filePath,
            'size'     => \filesize(__DIR__ . '/temp/' . $filePath),
        ];
    }

    TestUtils::setMember($request, 'files', $files);

    $module->apiWorkflowTemplateCreate($request, $response);
    ++$apiCalls;

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 1;
$request->setData('template', 1);

$module->apiWorkflowInstanceCreate($request, $response);
++$apiCalls;

echo \str_repeat('░', 10 - $p);
//endregion
