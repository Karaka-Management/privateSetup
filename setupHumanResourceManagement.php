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

use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\DateTime;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Setup human resource module
 */
//region HumanResource
/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\HumanResourceManagement\Controller\ApiController $module */
$module = $app->moduleManager->get('HumanResourceManagement');
TestUtils::setMember($module, 'app', $app);

$POSITIONS   = \count($variables['positions']);
$DEPARTMENTS = \count($variables['departments']);
$LOREM_COUNT = \count(Text::LOREM_IPSUM) - 1;

$count    = \count($variables['accounts']);
$interval = (int) \ceil($count / 10);
$z        = 0;
$p        = 0;

foreach ($variables['accounts'] as $account) {
    if (!\in_array('Employee', $account['groups'])) {
        continue;
    }

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 2;

    $request->setData('profiles', (string) ($account['profile'] ?? \mt_rand(1, 10)));
    $module->apiEmployeeCreate($request, $response);
    ++$apiCalls;

    $id = $response->get('')['response'][0]->getId();

    // company work history
    $history = \mt_rand(1, 3);

    $start = DateTime::generateDateTime(
        (new \DateTime())->setTimestamp(\time() - \mt_rand(31622400 * 5, 31622400 * 10)),
        (new \DateTime())->setTimestamp(\time() - \mt_rand(31622400 * 1, 31622400 * 4))
    );

    $end = DateTime::generateDateTime(
        $start,
        (new \DateTime())->setTimestamp($start->getTimestamp() + \mt_rand(1, 31622400))
    );

    for ($i = 0; $i < $history; ++$i) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('employee', $id);
        $request->setData('start', $start->format('Y-m-d'));
        $request->setData('end', $i + 1 < $history ? $end->format('Y-m-d') : null);
        $request->setData('unit', 2);
        $request->setData('department', $variables['departments'][$index = \mt_rand(0, $DEPARTMENTS - 1)]['id'] ?? ($index + 1));
        $request->setData('position', $variables['positions'][$index = \mt_rand(0, $POSITIONS - 1)]['id'] ?? ($index + 1));
        $module->apiEmployeeHistoryCreate($request, $response);
        ++$apiCalls;

        $start = clone $end;
        $end   = DateTime::generateDateTime(
            $start,
            (new \DateTime())->setTimestamp($start->getTimestamp() + \mt_rand(1, 31622400))
        );
    }

    // external work history
    $history = \mt_rand(0, 3);

    $start = DateTime::generateDateTime(
        (new \DateTime())->setTimestamp(\time() - \mt_rand(31622400 * 5, 31622400 * 10)),
        (new \DateTime())->setTimestamp(\time() - \mt_rand(31622400 * 1, 31622400 * 4))
    );

    $end = DateTime::generateDateTime(
        $start,
        (new \DateTime())->setTimestamp($start->getTimestamp() + \mt_rand(1, 31622400))
    );

    for ($i = 0; $i < $history; ++$i) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('employee', $id);
        $request->setData('start', $start->format('Y-m-d'));
        $request->setData('end', $i + 1 < $history ? $end->format('Y-m-d') : null);
        $request->setData('title', \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT - 1)]));
        $request->setData('address',
            \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)])
            . ' ' . \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)])
            . ' ' . \mt_rand(1, 1000)
        );
        $request->setData('postal', \str_pad((string) \mt_rand(1000, 99999), 5, '0', \STR_PAD_LEFT));
        $request->setData('city', \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)]));
        $request->setData('country', ISO3166TwoEnum::getRandom());
        $request->setData('state', '');

        $module->apiEmployeeWorkHistoryCreate($request, $response);
        ++$apiCalls;

        $start = clone $end;
        $end   = DateTime::generateDateTime(
            $start,
            (new \DateTime())->setTimestamp($start->getTimestamp() + \mt_rand(1, 31622400))
        );
    }

    // education history
    $history = \mt_rand(0, 3);

    $start = DateTime::generateDateTime(
        (new \DateTime())->setTimestamp(\time() - \mt_rand(31622400 * 5, 31622400 * 10)),
        (new \DateTime())->setTimestamp(\time() - \mt_rand(31622400 * 1, 31622400 * 4))
    );

    $end = DateTime::generateDateTime(
        $start,
        (new \DateTime())->setTimestamp($start->getTimestamp() + \mt_rand(1, 31622400))
    );

    for ($i = 0; $i < $history; ++$i) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('employee', $id);
        $request->setData('start', $start->format('Y-m-d'));
        $request->setData('end', $i + 1 < $history ? $end->format('Y-m-d') : null);
        $request->setData('title', \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT - 1)]));
        $request->setData('score', (string) \mt_rand(0, 100));
        $request->setData('address',
            \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)])
            . ' ' . \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)])
            . ' ' . \mt_rand(1, 1000)
        );
        $request->setData('postal', \str_pad((string) \mt_rand(1000, 99999), 5, '0', \STR_PAD_LEFT));
        $request->setData('city', \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)]));
        $request->setData('country', ISO3166TwoEnum::getRandom());
        $request->setData('state', '');

        $module->apiEmployeeEducationHistoryCreate($request, $response);
        ++$apiCalls;

        $start = clone $end;
        $end   = DateTime::generateDateTime(
            $start,
            (new \DateTime())->setTimestamp($start->getTimestamp() + \mt_rand(1, 31622400))
        );
    }

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

echo \str_repeat('░', 10 - $p);
//endregion
