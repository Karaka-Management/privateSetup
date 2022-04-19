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

use phpOMS\DataStorage\Database\DatabaseType;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;
use phpOMS\Utils\RnG\Text;

/**
 * Setup Exchange module
 *
 * @var \Modules\Media\Controller\ApiController $module
 */
//region Exchange
/** @var \Modules\Exchange\Controller\ApiController $module */
$module = $app->moduleManager->get('Exchange');
TestUtils::setMember($module, 'app', $app);

if (!\is_dir(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

$LOREM_COUNT = \count(Text::LOREM_IPSUM) - 1;

$exchanges = \scandir(__DIR__ . '/exchange/interfaces');

$count    = \count($exchanges);
$interval = (int) \ceil($count / 10);
$z        = 0;
$p        = 0;

foreach ($exchanges as $exchange) {
    if (!\is_dir(__DIR__ . '/exchange/interfaces/' . $exchange) || $exchange === '..' || $exchange === '.') {
        ++$z;
        if ($z % $interval === 0) {
            echo '░';
            ++$p;
        }

        continue;
    }

    $data = \json_decode(\file_get_contents(__DIR__ . '/exchange/interfaces/' . $exchange . '/interface.json'), true);

    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->header->account = 2;
    $request->setData('title', $data['name']);
    $request->setData('export', (bool) $data['export']);
    $request->setData('import', (bool) $data['import']);
    $request->setData('website', $data['website']);

    $files = [];

    $exchangeFiles = \scandir(__DIR__ . '/exchange/interfaces/' . $exchange);
    foreach ($exchangeFiles as $filePath) {
        if ($filePath === '..' || $filePath === '.') {
            continue;
        }

        if (\is_dir(__DIR__ . '/exchange/interfaces/' . $exchange . '/' . $filePath)) {
            $subdir = \scandir(__DIR__ . '/exchange/interfaces/' . $exchange . '/' . $filePath);
            foreach ($subdir as $subPath) {
                if (!\is_file(__DIR__ . '/exchange/interfaces/' . $exchange . '/' . $filePath . '/' . $subPath)) {
                    continue;
                }

                \copy(
                    __DIR__ . '/exchange/interfaces/' . $exchange . '/' . $filePath . '/' . $subPath,
                    __DIR__ . '/temp/' . $subPath
                );

                $files[] = [
                    'error'    => \UPLOAD_ERR_OK,
                    'type'     => \substr($subPath, \strrpos($subPath, '.') + 1),
                    'name'     => $filePath . '/' . $subPath,
                    'tmp_name' => __DIR__ . '/temp/' . $subPath,
                    'size'     => \filesize(__DIR__ . '/temp/' . $subPath),
                ];
            }
        } else {
            if (!\is_file(__DIR__ . '/exchange/interfaces/' . $exchange . '/' . $filePath)) {
                continue;
            }

            \copy(__DIR__ . '/exchange/interfaces/' . $exchange . '/' . $filePath, __DIR__ . '/temp/' . $filePath);

            $files[] = [
                'error'    => \UPLOAD_ERR_OK,
                'type'     => \substr($filePath, \strrpos($filePath, '.') + 1),
                'name'     => $filePath,
                'tmp_name' => __DIR__ . '/temp/' . $filePath,
                'size'     => \filesize(__DIR__ . '/temp/' . $filePath),
            ];
        }
    }

    TestUtils::setMember($request, 'files', $files);

    $module->apiInterfaceInstall($request, $response);
    ++$apiCalls;

    ++$z;
    if ($z % $interval === 0) {
        echo '░';
        ++$p;
    }
}

//region DatabaseExchanger Settings

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->header->account = 1;
$request->setData('id', 1);
$request->setData('title', 'Test Settings');

$setting = [
    'import' => [
        'db' => [
            'self' => true,
            'db' => '',
            'host' => '',
            'port' => 0,
            'login' => '',
            'password' => '',
            'database' => '',
        ]
    ],
    'export' => [
        'db' => [
            'self' => false,
            'db' => DatabaseType::MYSQL,
            'host' => '127.0.0.1',
            'port' => 3306,
            'login' => 'root',
            'password' => 'root',
            'database' => 'oms',
        ]
    ],
    'relation' => [
        [
            'src' => 'table_name1',
            'dest' => 'table_name1',
            'match'=> [
                [
                    'src_field' => [
                        'primary' => false,
                        'column' => 'column_name1',
                        'type' => 'TEXT',
                        'transform' => 'Y-m-d H:i:s',
                    ],
                    'dest_field' => [
                        'primary' => false,
                        'column' => 'column_name1',
                        'type' => 'TEXT',
                        'transform' => 'Y-m-d H:i:s',
                    ]
                ],
                [
                    'src_field' => [
                        'primary' => false,
                        'column' => 'column_name2',
                        'type' => 'TEXT',
                        'transform' => 'Y-m-d H:i:s',
                    ],
                    'dest_field' => [
                        'primary' => false,
                        'column' => 'column_name2',
                        'type' => 'TEXT',
                        'transform' => 'Y-m-d H:i:s',
                    ]
                ],
                [
                    'src_field' => [
                        'primary' => false,
                        'column' => 'column_name3',
                        'type' => 'TEXT',
                        'transform' => 'Y-m-d H:i:s',
                    ],
                    'dest_field' => [
                        'primary' => false,
                        'column' => 'column_name3',
                        'type' => 'TEXT',
                        'transform' => 'Y-m-d H:i:s',
                    ]
                ],
            ]
        ],
        [
            'src' => 'table_name2',
            'dest' => 'table_name2',
            'match' => [
                [
                    'src_field' => [
                        'primary' => false,
                        'column' => 'column_name',
                        'type' => 'TEXT',
                        'transform' => 'Y-m-d H:i:s',
                    ],
                    'dest_field' => [
                        'primary' => false,
                        'column' => 'column_name',
                        'type' => 'TEXT',
                        'transform' => 'Y-m-d H:i:s',
                    ]
                ]
            ]
        ]
    ]
];

$request->setData('data', \json_encode($setting));

$module->apiExchangeSettingCreate($request, $response);

//endregion

echo \str_repeat('░', 10 - $p);
//endregion
