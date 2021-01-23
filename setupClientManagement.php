<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
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
use phpOMS\System\MimeType;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\RnG\Email;
use phpOMS\Utils\RnG\Phone;
use phpOMS\Utils\RnG\Text;
use phpOMS\Utils\TestUtils;

/**
 * Setup accounts
 *
 * @var \Modules\ClientManagement\Controller\ApiController $module
 */
//region Accounts
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('ClientManagement');
TestUtils::setMember($module, 'app', $app);

$LOREM_COUNT = \count(Text::LOREM_IPSUM) - 1;
$CUSTOMERS   = 100;
$numbers     = [];

for ($i = 0; $i < $CUSTOMERS; ++$i) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 2;

    do {
        $number = \mt_rand(100000, 999999);
    } while (\in_array($number, $numbers));
    $numbers[] = $number;

    $request->setData('number', (string) $number);
    $request->setData('name1', \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT - 1)]));
    $request->setData('name2', \ucfirst(Text::LOREM_IPSUM[\mt_rand(0, $LOREM_COUNT - 1)]));

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

    $module->apiClientCreate($request, $response);

    $cId = $response->get('')['response']->getId();

    $count = \mt_rand(0, 4);
    for ($j = 0; $j < $count; ++$j) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 2;
        $request->setData('client', $cId);
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

    //region profile image
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    if (!\is_dir(__DIR__ . '/temp')) {
        \mkdir(__DIR__ . '/temp');
    }

    $image                 = \imagecreate(256, 256);
    $image_backgroundColor = \imagecolorallocate($image, 54, 151, 219);
    $image_textColor       = \imagecolorallocate($image, 52, 58, 64);

    \imagefill($image, 0, 0, $image_backgroundColor);
    \imagettftext(
        $image, 35, 0, 128 - 83, 128 + 15,
        $image_textColor,
        __DIR__ . '/files/SpaceMono-Bold.ttf',
        (string) $number
    );
    \imagepng($image, __DIR__ . '/temp/' . $number . '.png');
    \imagedestroy($image);

    $request->header->account = \mt_rand(2, 5);
    $request->setData('name', $number . ' backend');
    $request->setData('client', $cId);
    $request->setData('type', 'backend_image');

    TestUtils::setMember($request, 'files', [
        'file1' => [
            'name'     => $number . '_backend.png',
            'type'     => MimeType::M_PNG,
            'tmp_name' => __DIR__ . '/temp/' . $number . '.png',
            'error'    => \UPLOAD_ERR_OK,
            'size'     => \filesize(__DIR__ . '/temp/' . $number . '.png'),
        ],
    ]);

    $module->apiFileCreate($request, $response);
    //endregion
}
