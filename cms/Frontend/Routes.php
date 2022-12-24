<?php
declare(strict_types=1);

use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^/*$' => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:frontView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/solutions'        => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:solutionsView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/services'        => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:servicesView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/info'        => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:infoView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/shop'        => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:shopView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/contact'        => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:contactView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/imprint'        => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:imprintView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/privacy'        => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:privacyView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/terms'        => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:termsView',
            'verb' => RouteVerb::GET,
        ],
    ],
];