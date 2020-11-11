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

use Modules\Profile\Models\ContactType;
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Stdlib\Base\AddressType;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Email;
use phpOMS\Utils\RnG\Phone;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Setup accounts
 *
 * @var \Modules\SupplierManagement\Controller\ApiController $module
 */
//region Accounts
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('SupplierManagement');
TestUtils::setMember($module, 'app', $app);

$LOREM_COUNT = \count(Text::LOREM_IPSUM) - 1;
$SUPPLIERS   = 1000;
$numbers     = [];

for ($i = 0; $i < $SUPPLIERS; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->getHeader()->setAccount(2);

    do {
        $number = \mt_rand(100000, 999999);
    } while (\in_array($number, $numbers));
    $numbers[] = $number;

    $request->setData('number', (string) $number);
    $request->setData('name1', Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT - 1)]);
    $request->setData('name2', Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT - 1)]);

    $request->setData('type', AddressType::getRandom());
    $request->setData('address',
        \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)])
        . ' ' . \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)])
        . ' ' . \mt_rand(1, 1000)
    );
    $request->setData('postal', \str_pad((string) \mt_rand(1000, 99999), 5, '0', \STR_PAD_LEFT));
    $request->setData('city', \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)]));
    $request->setData('country', ISO3166TwoEnum::getRandom());
    $request->setData('state', '');

    $module->apiSupplierCreate($request, $response);

    $sId = $response->get('')['response']->getId();

    $count = \mt_rand(0, 4);
    for ($j = 0; $j < $count; ++$j) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->getHeader()->setAccount(2);
        $request->setData('supplier', $sId);
        $request->setData('type', $type = ContactType::getRandom());
        $request->setData('subtype', 0);

        if ($type === ContactType::PHONE) {
            $request->setData('content', Phone::generatePhone());
        } elseif ($type === ContactType::EMAIL) {
            $request->setData('content', Email::generateEmail());
        } elseif ($type === ContactType::FAX) {
            $request->setData('content', Phone::generatePhone());
        } elseif ($type === ContactType::WEBSITE) {
            $request->setData('content', 'https://' . Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)] . '.com');
        }

        $module->apiContactElementCreate($request, $response);
    }
}
