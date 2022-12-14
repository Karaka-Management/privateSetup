<?php
declare(strict_types=1);

use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^/*$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:frontView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/features'        => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:featureView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/pricing'        => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:pricingView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/signup'        => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:signupView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/signin'        => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:dashboardView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],
    '.*?/imprint'      => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:imprintView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/terms'        => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:termsView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/privacy'      => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:privacyView',
            'verb' => RouteVerb::GET,
        ],
    ],
    '.*?/contact'      => [
        [
            'dest' => '\Web\{APPNAME}\Controller\FrontendController:contactView',
            'verb' => RouteVerb::GET,
        ],
    ],

    '^/dashboard$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:dashboardView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],

    '^/admin/organizations$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:adminOrganizationsView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],
    '^/admin/users$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:adminUsersView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],
    '^/admin/resources$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:adminResourcesView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],
    '^/admin/bills$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:adminBillsView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],
    '^/admin/logs$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:adminLogsView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],

    '^/organization/settings$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:organizationSettingsView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],
    '^/organization/users$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:organizationUsersView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],
    '^/organization/users/\d+$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:organizationUsersEditView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],
    '^/organization/resources$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:organizationResourcesView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],
    '^/organization/bills$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:organizationBillsView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],

    '^/user/settings$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:userSettingsView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],
    '^/user/resource\?.*$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:userResourceView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],
    '^/user/resources$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:userResourcesView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],
    '^/user/resources/create$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:userResourcesCreateView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],
    '^/user/reports$'             => [
        [
            'dest' => '\Web\{APPNAME}\Controller\BackendController:userReportsView',
            'verb' => RouteVerb::GET,
            'permission' => [
                'type' => PermissionType::READ,
                'unit' => 1,
                'module' => '{APPNAME}',
            ]
        ],
    ],
];
