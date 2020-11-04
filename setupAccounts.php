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

use Modules\Admin\Models\GroupMapper;
use Modules\Profile\Models\ContactElement;
use Modules\Profile\Models\ContactType;
use phpOMS\Account\AccountStatus;
use phpOMS\Account\AccountType;
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Stdlib\Base\AddressType;
use phpOMS\System\MimeType;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Phone;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Setup accounts
 *
 * @var \Modules\Admin\Controller\ApiController $module
 */
//region Accounts
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Admin');
TestUtils::setMember($module, 'app', $app);

$accounts = [
    [
        'login'  => 'guest',
        'pass'   => 'guest',
        'name1'  => 'Test',
        'name2'  => 'Guest',
        'image'  => 't_guest.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.guest@orange-management.email',
        'groups' => [],
    ],
    [
        'login'  => 'user',
        'pass'   => 'user',
        'name1'  => 'Test',
        'name2'  => 'User',
        'image'  => 't_user.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.user@orange-management.email',
        'groups' => ['user'],
    ],
    [
        'login'  => 'supplier',
        'pass'   => 'supplier',
        'name1'  => 'Test',
        'name2'  => 'Supplier',
        'image'  => 't_supplier.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.supplier@orange-management.email',
        'groups' => ['user'],
    ],
    [
        'login'  => 'client',
        'pass'   => 'client',
        'name1'  => 'Test',
        'name2'  => 'Client',
        'image'  => 't_client.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.client@orange-management.email',
        'groups' => ['user'],
    ],
    [
        'login'  => 'support',
        'pass'   => 'support',
        'name1'  => 'Test',
        'name2'  => 'Support',
        'image'  => 't_support.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.support@orange-management.email',
        'groups' => ['user', 'Suppoer', 'Employee', 'VKL'],
    ],
    [
        'login'  => 'secretary',
        'pass'   => 'secretary',
        'name1'  => 'Test',
        'name2'  => 'Secretary',
        'image'  => 't_secretary.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.secretary@orange-management.email',
        'groups' => ['user', 'Secretariat', 'Employee'],
    ],
    [
        'login'  => 'service',
        'pass'   => 'service',
        'name1'  => 'Test',
        'name2'  => 'Service',
        'image'  => 't_service.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.service@orange-management.email',
        'groups' => ['user', 'Service', 'Employee', 'VKL'],
    ],
    [
        'login'  => 'finance',
        'pass'   => 'finance',
        'name1'  => 'Test',
        'name2'  => 'Finance',
        'image'  => 't_finance.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.finance@orange-management.email',
        'groups' => ['user', 'Executive', 'Finance', 'Controlling', 'Employee', 'VKL'],
    ],
    [
        'login'  => 'sales',
        'pass'   => 'sales',
        'name1'  => 'Test',
        'name2'  => 'Sales',
        'image'  => 't_sales.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.sales@orange-management.email',
        'groups' => ['user', 'Executive', 'Sales', 'Employee', 'VKL'],
    ],
    [
        'login'  => 'purchase',
        'pass'   => 'purchase',
        'name1'  => 'Test',
        'name2'  => 'Purchase',
        'image'  => 't_purchase.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.purchase@orange-management.email',
        'groups' => ['user', 'Executive', 'Purchasing', 'Employee'],
    ],
    [
        'login'  => 'warehouse',
        'pass'   => 'warehouse',
        'name1'  => 'Test',
        'name2'  => 'Warehouse',
        'image'  => 't_warehouse.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.warehouse@orange-management.email',
        'groups' => ['user', 'Warehouse', 'Employee'],
    ],
    [
        'login'  => 'marketing',
        'pass'   => 'marketing',
        'name1'  => 'Test',
        'name2'  => 'Marketing',
        'image'  => 't_marketing.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.marketing@orange-management.email',
        'groups' => ['user', 'Executive', 'Marketing', 'Employee', 'VKL'],
    ],
    [
        'login'  => 'production',
        'pass'   => 'production',
        'name1'  => 'Test',
        'name2'  => 'Production',
        'image'  => 't_production.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.production@orange-management.email',
        'groups' => ['user', 'Executive', 'Production', 'Employee'],
    ],
    [
        'login'  => 'salesrep',
        'pass'   => 'salesrep',
        'name1'  => 'Test',
        'name2'  => 'Salesrep',
        'image'  => 't_salesrep.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.salesrep@orange-management.email',
        'groups' => ['user', 'Sales', 'Employee'],
    ],
];

/** @var \Modules\Profile\Controller\ApiController $profileModule */
$profileModule = $app->moduleManager->get('Profile');
TestUtils::setMember($profileModule, 'app', $app);

$LOREM_COUNT = count(Text::LOREM_IPSUM) - 1;

$groups = GroupMapper::getAll();
foreach ($accounts as $account) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->getHeader()->setAccount(1);
    $request->setData('login', $account['login']);
    $request->setData('password', $account['pass']);
    $request->setData('name1', $account['name1']);
    $request->setData('name2', $account['name2']);
    $request->setData('name3', $account['name3'] ?? '');
    $request->setData('email', $account['email']);
    $request->setData('locale', $account['locale'] ?? null);
    $request->setData('type', AccountType::USER);
    $request->setData('status', $account['status'] ?? AccountStatus::INACTIVE);
    $module->apiAccountCreate($request, $response);

    //region groups
    $a = $response->get('')['response'];
    foreach ($groups as $g) {
        if (\in_array($g->getName(), $account['groups'])) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->getHeader()->setAccount($a->getId());
            $request->setData('account', $a->getId());
            $request->setData('igroup-idlist', (string) $g->getId());

            $module->apiAddGroupToAccount($request, $response);
        }
    }
    //endregion

    //region user image
    if (isset($account['image'])) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        if (!\file_exists(__DIR__ . '/temp')) {
            \mkdir(__DIR__ . '/temp');
        }

        $image = imagecreate(256, 256);
        $image_backgroundColor = imagecolorallocate($image, 54, 151, 219);
        $image_textColor       = imagecolorallocate($image, 52, 58, 64);

        imagefill($image, 0, 0, $image_backgroundColor);
        imagettftext($image, 100, 0, 128 - 83, 128 + 50, $image_textColor, __DIR__ . '/files/SpaceMono-Bold.ttf', \strtoupper($account['name1'][0] . $account['name2'][0]));
        imagepng($image, __DIR__ . '/temp/' . $account['image']);
        imagedestroy($image);

        $request->getHeader()->setAccount($a->getId());
        $request->setData('name', 'Profile Image');

        TestUtils::setMember($request, 'files', [
            'file1' => [
                'name'     => 'Profie Image.png',
                'type'     => MimeType::M_PNG,
                'tmp_name' => __DIR__ . '/temp/' . $account['image'],
                'error'    => \UPLOAD_ERR_OK,
                'size'     => \filesize(__DIR__ . '/temp/' . $account['image']),
            ],
        ]);

        $profileModule->apiSettingsAccountImageSet($request, $response);
    }
    //endregion

    //region address
    $count = \mt_rand(0, 2);
    for ($i = 0; $i < $count; ++$i) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->getHeader()->setAccount($a->getId());
        $request->setData('account', $a->getId());
        $request->setData('type', AddressType::getRandom());
        $request->setData('name', '');
        $request->setData('addition', '');
        $request->setData('address', \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)]) . ' ' . \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)]) . ' ' . \mt_rand(1, 1000));
        $request->setData('postal', \str_pad((string) \mt_rand(1000, 99999), 5, '0', \STR_PAD_LEFT));
        $request->setData('city', \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)]));
        $request->setData('country', ISO3166TwoEnum::getRandom());
        $request->setData('state', '');

        $profileModule->apiAddressCreate($request, $response);
    }
    //endregion

    //region contact elements
    $count = \mt_rand(0, 4);
    for ($i = 0; $i < $count; ++$i) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->getHeader()->setAccount($a->getId());
        $request->setData('account', $a->getId());
        $request->setData('type', $type = ContactType::getRandom());
        $request->setData('subtype', 0);

        if ($type === ContactType::PHONE) {
            $request->setData('content', Phone::generatePhone());
        } elseif ($type === ContactType::EMAIL) {
            $request->setData('content', Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)] . '@' . Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)] . '.com');
        } elseif ($type === ContactType::FAX) {
            $request->setData('content', Phone::generatePhone());
        } elseif ($type === ContactType::WEBSITE) {
            $request->setData('content', 'https://' . Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT)] . '.com');
        }

        $profileModule->apiContactElementCreate($request, $response);

    }
    //endregion
}
//endregion
