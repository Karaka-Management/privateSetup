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
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Admin\Models\GroupMapper;
use Modules\Profile\Models\ContactType;
use phpOMS\Account\AccountStatus;
use phpOMS\Account\AccountType;
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Stdlib\Base\AddressType;
use phpOMS\System\MimeType;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Email;
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
/** @var \Modules\Admin\Controller\ApiController $module */
$module = $app->moduleManager->get('Admin');
TestUtils::setMember($module, 'app', $app);

$accounts = $variables['accounts'];

/** @var \Modules\Profile\Controller\ApiController $profileModule */
$profileModule = $app->moduleManager->get('Profile');
TestUtils::setMember($profileModule, 'app', $app);

$count    = \count($accounts);
$interval = (int) \ceil($count / 10);
$z        = 0;
$p        = 0;

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

/** @var \Modules\Admin\Models\Group[] $groups */
$groups = GroupMapper::getAll()->execute();
foreach ($accounts as $key=> $account) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 1;
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
    ++$apiCalls;

    //region groups
    $a                                      = $response->get('')['response'];
    $variables['accounts'][$key]['id']      = $a->getId();
    $variables['accounts'][$key]['profile'] = \Modules\Profile\Models\ProfileMapper::get()->where('account', $a->getId())->execute()->getId();

    foreach ($groups as $g) {
        if (\in_array($g->name, $account['groups'])) {
            $response = new HttpResponse();
            $request  = new HttpRequest(new HttpUri(''));

            $request->header->account = $a->getId();
            $request->setData('account', $a->getId());
            $request->setData('igroup-idlist', (string) $g->getId());

            $module->apiAddGroupToAccount($request, $response);
            ++$apiCalls;
        }
    }
    //endregion

    //region user image
    if (isset($account['image'])) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = $a->getId();
        $request->setData('name', 'Profile Image');

        \copy(__DIR__ . '/accounts/' . $account['image'], __DIR__ . '/temp/' . $account['image']);

        TestUtils::setMember($request, 'files', [
            'file1' => [
                'name'     => $account['image'],
                'type'     => MimeType::M_PNG,
                'tmp_name' => __DIR__ . '/temp/' . $account['image'],
                'error'    => \UPLOAD_ERR_OK,
                'size'     => \filesize(__DIR__ . '/temp/' . $account['image']),
            ],
        ]);

        $profileModule->apiSettingsAccountImageSet($request, $response);
        ++$apiCalls;
    }
    //endregion

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

unset($groups);

echo \str_repeat('░', 10 - $p);
//endregion
