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

use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Setup accounts
 *
 * @var \Modules\Contract\Controller\ApiController $module
 */
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\ContractManagement\Controller\ApiController $module */
$module = $app->moduleManager->get('ContractManagement');
TestUtils::setMember($module, 'app', $app);

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

$LOREM = \array_slice(Text::LOREM_IPSUM, 0, 50);

$LOREM_COUNT = \count($LOREM) - 1;
$ITEMS       = 10;
$numbers     = [];

$count    = \count($LOREM);
$interval = (int) \ceil($count / 2);
$z        = 0;
$p        = 0;

// contract types (e.g. color, material etc.)
foreach ($LOREM as $word) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = \mt_rand(2, 5);

    $request->setData('language', ISO639x1Enum::_EN);
    $request->setData('title', 'EN:' . $word);

    $module->apiContractTypeCreate($request, $response);
    ++$apiCalls;

    $contractTypeId = $response->get('')['response']->getId();
    foreach ($variables['languages'] as $language) {
        if ($language === ISO639x1Enum::_EN) {
            continue;
        }

        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = \mt_rand(2, 5);

        $request->setData('type', $contractTypeId);
        $request->setData('language', $language);
        $request->setData('title', \strtoupper($language) . ':' . $LOREM[\mt_rand(0, $LOREM_COUNT)]);

        $module->apiContractTypeL11nCreate($request, $response);
        ++$apiCalls;
    }

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo $p < 2 ? '░' : '';

$count    = 100;
$interval = (int) \ceil($count / 8);
$z        = 0;
$p        = 0;

for ($i = 0; $i < 100; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $MARKDOWN = \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6');

    $request->header->account = $user = \mt_rand(2, 5);
    $request->setData('title', $title = \trim(\strtok($MARKDOWN, "\n"), ' #'));
    $request->setData('description', \preg_replace('/^.+\n/', '', $MARKDOWN));
    $request->setData('costs', 0);
    $request->setData('duration', 365);
    $request->setData('warning', 0);
    $request->setData('type', \mt_rand(1, $LOREM_COUNT));
    $request->setData('autorenewal_when', '1');
    $request->setData('autorenewal_duration', '1');
    $request->setData('autorenewal_times', '0');
    $request->setData('responsible', \mt_rand(2, 5));

    $start = new \DateTime('now');
    $start->setTimestamp($start->getTimestamp() + \mt_rand(-31536000 * 1, 31536000));
    $request->setData('start', $start->format('Y-m-d'));

    $end = new \DateTime('now');
    $end->setTimestamp($start->getTimestamp() + \mt_rand(31536000 / 4, 31536000 * 2));
    $request->setData('end', $end->format('Y-m-d'));

    if (\mt_rand(1, 100) < 51) {
        $request->setData('account', \mt_rand(1, \count($variables['accounts']) - 1));
    }

    $module->apiContractCreate($request, $response);
    ++$apiCalls;

    $cId = $response->get('')['response']->getId();

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = $user;
    $request->setData('contract', $cId);
    $request->setData('contract_title', $title);

    $file = 'Pdf.pdf';
	\copy(__DIR__ . '/media/types/' . $file, __DIR__ . '/temp/' . $file);

    TestUtils::setMember($request, 'files', [
        'file1' => [
            'name'     => $file,
            'type'     => \explode('.', $file)[1],
            'tmp_name' => __DIR__ . '/temp/' . $file,
            'error'    => \UPLOAD_ERR_OK,
            'size'     => \filesize(__DIR__ . '/temp/' . $file),
        ],
    ]);

    $module->apiContractDocumentCreate($request, $response);
    ++$apiCalls;

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo $p < 8 ? '░' : '';
