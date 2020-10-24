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
use phpOMS\Account\AccountStatus;
use phpOMS\Account\AccountType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\System\MimeType;
use phpOMS\Uri\HttpUri;
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
        'image'  => 'avatar_user.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.guest@orange-management.email',
        'groups' => ['user', 'Executive', 'Finance', 'Controlling', 'Employee', 'VKL', 'beta_tester'],
    ],
    [
        'login'  => 'user',
        'pass'   => 'user',
        'name1'  => 'Test',
        'name2'  => 'User',
        'image'  => 'avatar_user.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.user@orange-management.email',
        'groups' => ['user', 'Executive', 'Finance', 'Controlling', 'Employee', 'VKL', 'beta_tester'],
    ],
    [
        'login'  => 'supplier',
        'pass'   => 'supplier',
        'name1'  => 'Test',
        'name2'  => 'Supplier',
        'image'  => 'avatar_user.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.supplier@orange-management.email',
        'groups' => ['user', 'Executive', 'Finance', 'Controlling', 'Employee', 'VKL', 'beta_tester'],
    ],
    [
        'login'  => 'client',
        'pass'   => 'client',
        'name1'  => 'Test',
        'name2'  => 'Client',
        'image'  => 'avatar_user.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.client@orange-management.email',
        'groups' => ['user', 'Executive', 'Finance', 'Controlling', 'Employee', 'VKL', 'beta_tester'],
    ],
    [
        'login'  => 'support',
        'pass'   => 'support',
        'name1'  => 'Test',
        'name2'  => 'Support',
        'image'  => 'avatar_user.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.support@orange-management.email',
        'groups' => ['user', 'Executive', 'Finance', 'Controlling', 'Employee', 'VKL', 'beta_tester'],
    ],
    [
        'login'  => 'finance',
        'pass'   => 'finance',
        'name1'  => 'Test',
        'name2'  => 'Finance',
        'image'  => 'avatar_user.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.finance@orange-management.email',
        'groups' => ['user', 'Executive', 'Finance', 'Controlling', 'Employee', 'VKL', 'beta_tester'],
    ],
    [
        'login'  => 'sales',
        'pass'   => 'sales',
        'name1'  => 'Test',
        'name2'  => 'Sales',
        'image'  => 'avatar_user.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.sales@orange-management.email',
        'groups' => ['user', 'Executive', 'Finance', 'Controlling', 'Employee', 'VKL', 'beta_tester'],
    ],
    [
        'login'  => 'purchase',
        'pass'   => 'purchase',
        'name1'  => 'Test',
        'name2'  => 'Purchase',
        'image'  => 'avatar_user.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.purchase@orange-management.email',
        'groups' => ['user', 'Executive', 'Finance', 'Controlling', 'Employee', 'VKL', 'beta_tester'],
    ],
    [
        'login'  => 'warehouse',
        'pass'   => 'warehouse',
        'name1'  => 'Test',
        'name2'  => 'Warehouse',
        'image'  => 'avatar_user.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.warehouse@orange-management.email',
        'groups' => ['user', 'Executive', 'Finance', 'Controlling', 'Employee', 'VKL', 'beta_tester'],
    ],
    [
        'login'  => 'marketing',
        'pass'   => 'marketing',
        'name1'  => 'Test',
        'name2'  => 'Marketing',
        'image'  => 'avatar_user.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.marketing@orange-management.email',
        'groups' => ['user', 'Executive', 'Finance', 'Controlling', 'Employee', 'VKL', 'beta_tester'],
    ],
    [
        'login'  => 'production',
        'pass'   => 'production',
        'name1'  => 'Test',
        'name2'  => 'Production',
        'image'  => 'avatar_user.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.production@orange-management.email',
        'groups' => ['user', 'Executive', 'Finance', 'Controlling', 'Employee', 'VKL', 'beta_tester'],
    ],
    [
        'login'  => 'salesrep',
        'pass'   => 'salesrep',
        'name1'  => 'Test',
        'name2'  => 'Salesrep',
        'image'  => 'avatar_user.png',
        'status' => AccountStatus::ACTIVE,
        'email'  => 't.salesrep@orange-management.email',
        'groups' => ['user', 'Executive', 'Finance', 'Controlling', 'Employee', 'VKL', 'beta_tester'],
    ],
];

/** @var \Modules\Profile\Controller\ApiController $profileModule */
$profileModule = $app->moduleManager->get('Profile');
TestUtils::setMember($profileModule, 'app', $app);

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

    //region User image
    if (isset($account['image'])) {
        $responseProfile = new HttpResponse();
        $requestProfile  = new HttpRequest(new HttpUri(''));

        if (!\file_exists(__DIR__ . '/temp')) {
            \mkdir(__DIR__ . '/temp');
        }

        \copy(__DIR__ . '/img/' . $account['image'], __DIR__ . '/temp/' . $account['image']);

        $requestProfile->getHeader()->setAccount($response->get('')['response']->getId());
        $requestProfile->setData('name', 'Profile Image');

        TestUtils::setMember($requestProfile, 'files', [
            'file1' => [
                'name'     => 'Profie Image.png',
                'type'     => MimeType::M_PNG,
                'tmp_name' => __DIR__ . '/temp/' . $account['image'],
                'error'    => \UPLOAD_ERR_OK,
                'size'     => \filesize(__DIR__ . '/img/' . $account['image']),
            ],
        ]);

        $profileModule->apiSettingsAccountImageSet($requestProfile, $responseProfile);
    }
    //endregion

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
}
//endregion
