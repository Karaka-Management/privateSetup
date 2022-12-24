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

use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\IO\Zip\Zip;
use phpOMS\Utils\TestUtils;
use phpOMS\Localization\ISO639x1Enum;

/**
 * Create applications
 */
//region Applications

/** @var \phpOMS\Application\ApplicationAbstract $app */
/** @var \Modules\CMS\Controller\ApiController $module */
$module = $app->moduleManager->get('CMS');
TestUtils::setMember($module, 'app', $app);

/* Frontend */
// Upload files
Zip::pack([__DIR__ . '/cms/Frontend' => '/'], __DIR__ . '/temp/Frontend.zip');

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 1;
$request->setData('name', 'Frontend');

$files = [
    [
        'error'    => \UPLOAD_ERR_OK,
        'type'     => 'zip',
        'name'     => 'Frontend.zip',
        'tmp_name' => __DIR__ . '/temp/Frontend.zip',
        'size'     => \filesize(__DIR__ . '/temp/Frontend.zip'),
    ],
];

TestUtils::setMember($request, 'files', $files);

$module->apiApplicationInstall($request, $response);
++$apiCalls;

$appId = $response->get('')['response']->getId();

if (\is_file(__DIR__ . '/temp/Frontend.zip')) {
    \unlink(__DIR__ . '/temp/Frontend.zip');
}

// Create content
$pages = [
    [
        'name' => 'frontpage',
        'template' => 'tpl/front.tpl.php',
        'l11n' => [
            [
                'name' => 'front',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/frontend.en.php'),
                'language' => ISO639x1Enum::_EN
            ],
            [
                'name' => 'front',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/frontend.de.php'),
                'language' => ISO639x1Enum::_DE
            ],
        ],
    ],
    [
        'name' => 'solutions_list',
        'template' => 'tpl/solutions.tpl.php',
        'l11n' => [
            [
                'name' => 'solutions',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/solutions.en.php'),
                'language' => ISO639x1Enum::_EN
            ],
            [
                'name' => 'solutions',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/solutions.de.php'),
                'language' => ISO639x1Enum::_DE
            ],
        ],
    ],
    [
        'name' => 'services_list',
        'template' => 'tpl/services.tpl.php',
        'l11n' => [
            [
                'name' => 'services',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/services.en.php'),
                'language' => ISO639x1Enum::_EN
            ],
            [
                'name' => 'services',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/services.de.php'),
                'language' => ISO639x1Enum::_DE
            ],
        ],
    ],
    [
        'name' => 'footer',
        'template' => 'tpl/footer.tpl.php',
        'l11n' => [
            [
                'name' => 'about',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/footer_about.en.md'),
                'language' => ISO639x1Enum::_EN
            ],
            [
                'name' => 'about',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/footer_about.de.md'),
                'language' => ISO639x1Enum::_DE
            ],
        ],
    ],
    [
        'name' => 'imprint',
        'template' => '',
        'l11n' => [
            [
                'name' => 'imprint',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/imprint.en.md'),
                'language' => ISO639x1Enum::_EN
            ],
            [
                'name' => 'imprint',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/imprint.de.md'),
                'language' => ISO639x1Enum::_DE
            ],
        ],
    ],
    [
        'name' => 'terms',
        'template' => '',
        'l11n' => [
            [
                'name' => 'terms',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/terms.en.md'),
                'language' => ISO639x1Enum::_EN
            ],
            [
                'name' => 'terms',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/terms.de.md'),
                'language' => ISO639x1Enum::_DE
            ],
        ],
    ],
    [
        'name' => 'privacy',
        'template' => '',
        'l11n' => [
            [
                'name' => 'privacy',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/privacy.en.md'),
                'language' => ISO639x1Enum::_EN
            ],
            [
                'name' => 'privacy',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/privacy.de.md'),
                'language' => ISO639x1Enum::_DE
            ],
        ],
    ],
];

foreach ($pages as $page) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 1;
    $request->setData('name', $page['name']);
    $request->setData('template', $page['template']);
    $request->setData('app', $appId);

    $module->apiPageCreate($request, $response);
    ++$apiCalls;

    $pageId = $response->get('')['response']->getId();

    foreach ($page['l11n'] as $l11n) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('page', $pageId);
        $request->setData('name', $l11n['name']);
        $request->setData('content', $l11n['content']);
        $request->setData('language', $l11n['language']);

        $module->apiPageL11nCreate($request, $response);
        ++$apiCalls;
    }
}

echo '░░░░░';

/* ORW */
Zip::pack([__DIR__ . '/cms/Orw' => '/'], __DIR__ . '/temp/Orw.zip');

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 1;
$request->setData('name', 'Orw');

$files = [
    [
        'error'    => \UPLOAD_ERR_OK,
        'type'     => 'zip',
        'name'     => 'Orw.zip',
        'tmp_name' => __DIR__ . '/temp/Orw.zip',
        'size'     => \filesize(__DIR__ . '/temp/Orw.zip'),
    ],
];

TestUtils::setMember($request, 'files', $files);

$module->apiApplicationInstall($request, $response);
++$apiCalls;

$appId = $response->get('')['response']->getId();

if (\is_file(__DIR__ . '/temp/Orw.zip')) {
    \unlink(__DIR__ . '/temp/Orw.zip');
}

// Create content

$pages = [
    [
        'name' => 'frontpage',
        'template' => 'tpl/front.tpl.php',
        'l11n' => [
            [
                'name' => 'front',
                'content' => \file_get_contents(__DIR__ . '/content/cms/orw/frontend.en.php'),
                'language' => ISO639x1Enum::_EN
            ],
            [
                'name' => 'front',
                'content' => \file_get_contents(__DIR__ . '/content/cms/orw/frontend.de.php'),
                'language' => ISO639x1Enum::_DE
            ],
        ],
    ],
    [
        'name' => 'features',
        'template' => 'tpl/features.tpl.php',
        'l11n' => [
            [
                'name' => 'features',
                'content' => \file_get_contents(__DIR__ . '/content/cms/orw/features.en.php'),
                'language' => ISO639x1Enum::_EN
            ],
            [
                'name' => 'features',
                'content' => \file_get_contents(__DIR__ . '/content/cms/orw/features.de.php'),
                'language' => ISO639x1Enum::_DE
            ],
        ],
    ],
    [
        'name' => 'pricing',
        'template' => 'tpl/pricing.tpl.php',
        'l11n' => [
            [
                'name' => 'pricing',
                'content' => \file_get_contents(__DIR__ . '/content/cms/orw/pricing.en.php'),
                'language' => ISO639x1Enum::_EN
            ],
            [
                'name' => 'pricing',
                'content' => \file_get_contents(__DIR__ . '/content/cms/orw/pricing.de.php'),
                'language' => ISO639x1Enum::_DE
            ],
        ],
    ],
    [
        'name' => 'imprint',
        'template' => '',
        'l11n' => [
            [
                'name' => 'imprint',
                'content' => \file_get_contents(__DIR__ . '/content/cms/orw/imprint.en.md'),
                'language' => ISO639x1Enum::_EN
            ],
            [
                'name' => 'imprint',
                'content' => \file_get_contents(__DIR__ . '/content/cms/orw/imprint.de.md'),
                'language' => ISO639x1Enum::_DE
            ],
        ],
    ],
    [
        'name' => 'terms',
        'template' => '',
        'l11n' => [
            [
                'name' => 'terms',
                'content' => \file_get_contents(__DIR__ . '/content/cms/orw/terms.en.md'),
                'language' => ISO639x1Enum::_EN
            ],
            [
                'name' => 'terms',
                'content' => \file_get_contents(__DIR__ . '/content/cms/orw/terms.de.md'),
                'language' => ISO639x1Enum::_DE
            ],
        ],
    ],
    [
        'name' => 'privacy',
        'template' => '',
        'l11n' => [
            [
                'name' => 'privacy',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/privacy.en.md'),
                'language' => ISO639x1Enum::_EN
            ],
            [
                'name' => 'privacy',
                'content' => \file_get_contents(__DIR__ . '/content/cms/frontend/privacy.de.md'),
                'language' => ISO639x1Enum::_DE
            ],
        ],
    ],
];

foreach ($pages as $page) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 1;
    $request->setData('name', $page['name']);
    $request->setData('template', $page['template']);
    $request->setData('app', $appId);

    $module->apiPageCreate($request, $response);
    ++$apiCalls;

    $pageId = $response->get('')['response']->getId();

    foreach ($page['l11n'] as $l11n) {
        $response = new HttpResponse();
        $request  = new HttpRequest(new HttpUri(''));

        $request->header->account = 1;
        $request->setData('page', $pageId);
        $request->setData('name', $l11n['name']);
        $request->setData('content', $l11n['content']);
        $request->setData('language', $l11n['language']);

        $module->apiPageL11nCreate($request, $response);
        ++$apiCalls;
    }
}

echo '░░░░░';
//endregion

$module = $app->moduleManager->get('Admin');
TestUtils::setMember($module, 'app', $app);

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 1;
$request->setData('settings', \json_encode([
    ['path' => 'app/default/app', 'value' => 'Frontend'],
    ['path' => 'app/default/id', 'value' => 'frontend'],
    ['path' => 'app/domains/127.0.0.1/app', 'value' => 'Frontend'],
    ['path' => 'app/domains/127.0.0.1/id', 'value' => 'frontend'],
]));

$module->apiAppConfigSet($request, $response);
++$apiCalls;
