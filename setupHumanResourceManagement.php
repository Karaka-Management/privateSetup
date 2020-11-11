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

use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\DateTime;
use phpOMS\Utils\TestUtils;

/**
 * Setup human resource module
 *
 * @var \Modules\HumanResourceManagement\Controller\ApiController $module
 */
//region HumanResource
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('HumanResourceManagement');
TestUtils::setMember($module, 'app', $app);

$POSITIONS   = \count($variables['positions']);
$DEPARTMENTS = \count($variables['departments']);

foreach ($variables['accounts'] as $account) {
    if (!\in_array('Employee', $account['groups'])) {
        continue;
    }

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->getHeader()->setAccount(2);

    $request->setData('profiles', (string) $account['profile']);
    $module->apiEmployeeCreate($request, $response);

    $id      = $response->get('')['response'][0]->getId();
    $history = \mt_rand(-2, 3);

    $start = DateTime::generateDateTime(
        (new \DateTime())->setTimestamp(\time() - \mt_rand(31622400*5, 31622400*10)),
        (new \DateTime())->setTimestamp(\time() - \mt_rand(31622400*1, 31622400*4))
    );

    $end = DateTime::generateDateTime(
        $start,
        (new \DateTime())->setTimestamp($start->getTimestamp() + \mt_rand(1, 31622400))
    );

    for ($i = 0; $i < $history; ++$i) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->getHeader()->setAccount(1);
        $request->setData('employee', $id);
        $request->setData('start', $start->format('Y-m-d'));
        $request->setData('end', $i + 1 < $history ? $end->format('Y-m-d') : null);
        $request->setData('unit', 2);
        $request->setData('department', $variables['departments'][\mt_rand(0, $DEPARTMENTS - 1)]['id']);
        $request->setData('position', $variables['positions'][\mt_rand(0, $POSITIONS - 1)]['id']);
        $module->apiEmployeeHistoryCreate($request, $response);

        $start = clone $end;
        $end   = DateTime::generateDateTime(
            $start,
            (new \DateTime())->setTimestamp($start->getTimestamp() + \mt_rand(1, 31622400))
        );
    }
}
//endregion
