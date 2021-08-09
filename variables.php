<?php declare(strict_types=1);

use phpOMS\Account\AccountStatus;
use phpOMS\Localization\ISO639x1Enum;

return [
    'languages' => [
        ISO639x1Enum::_EN, // English
        ISO639x1Enum::_ZH, // Chinese
        ISO639x1Enum::_JA, // Japanese
        ISO639x1Enum::_DE, // German
/*        ISO639x1Enum::_IT, // Italian
        ISO639x1Enum::_ES, // Spanish
        ISO639x1Enum::_PT, // Portuguese
        ISO639x1Enum::_FR, // French
        ISO639x1Enum::_RU, // Russian
        ISO639x1Enum::_PL, // Polish
//        ISO639x1Enum::_UK, // Ukrainian
        ISO639x1Enum::_CS, // Czech
        ISO639x1Enum::_EL, // Greek
        ISO639x1Enum::_TR, // Turkish
        ISO639x1Enum::_AR, // Arabic
        ISO639x1Enum::_KO, // Korean
        ISO639x1Enum::_SV, // Swedish
//        ISO639x1Enum::_DA, // Danish
//        ISO639x1Enum::_FI, // Finnish
        ISO639x1Enum::_NO, // Norwegian
        ISO639x1Enum::_HI, // Hindi
//        ISO639x1Enum::_JV, // Javanese
//        ISO639x1Enum::_HU, // Hungarian */
    ],
    'mFiles' => [],
    'colors' => [
        '#ff7979',
        '#badc58',
        '#f9ca24',
        '#f0932b',
        '#eb4d4b',
        '#6ab04c',
        '#7ed6df',
        '#e056fd',
        '#686de0',
        '#30336b',
        '#95afc0',
        '#22a6b3',
        '#be2edd',
        '#4834d4',
        '#130f40',
        '#535c68',
    ],
    'accounts' => [
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
    ],
    'departments' => [
        ['name' => 'Management',            'parent' => null],
        ['name' => 'R&D',                   'parent' => 'Management'],
        ['name' => 'Sales Domestic',        'parent' => 'Management'],
        ['name' => 'Sales Reps.',           'parent' => 'Sales Domestic'],
        ['name' => 'Domestic Back-Office',  'parent' => 'Sales Domestic'],
        ['name' => 'Precious Alloys/IMPLA', 'parent' => 'Domestic Back-Office'],
        ['name' => 'Domestic Invoicing',    'parent' => 'Domestic Back-Office'],
        ['name' => 'Sales Export',          'parent' => 'Management'],
        ['name' => 'Area Managers',         'parent' => 'Sales Export'],
        ['name' => 'Export Back-Office',    'parent' => 'Sales Export'],
        ['name' => 'Service',               'parent' => 'Management'],
        ['name' => 'Support',               'parent' => 'Management'],
        ['name' => 'Purchasing',            'parent' => 'Management'],
        ['name' => 'Warehouse',             'parent' => 'Purchasing'],
        ['name' => 'Secretariat',           'parent' => 'Management'],
        ['name' => 'Registration',          'parent' => 'Secretariat'],
        ['name' => 'Production',            'parent' => 'Management'],
        ['name' => 'Reception',             'parent' => 'Secretariat'],
        ['name' => 'HR',                    'parent' => 'Management'],
        ['name' => 'QA',                    'parent' => 'Management'],
        ['name' => 'QM',                    'parent' => 'Management'],
        ['name' => 'Finance',               'parent' => 'Management'],
        ['name' => 'Accounts Receivable',   'parent' => 'Finance'],
        ['name' => 'Accounts Payable',      'parent' => 'Finance'],
        ['name' => 'Marketing',             'parent' => 'Management'],
        ['name' => 'IT',                    'parent' => 'Management'],
    ],
    'positions' => [
        ['name' => 'CEO',                     'department' => 'Management',            'parent' => null],
        ['name' => 'Executive Member',        'department' => null,                    'parent' => 'CEO'],
        ['name' => 'COO',                     'department' => 'Management',            'parent' => 'CEO'],
        ['name' => 'CTO',                     'department' => 'R&D',                   'parent' => 'CEO'],
        ['name' => 'R&D Employee',            'department' => 'R&D',                   'parent' => 'CTO'],
        ['name' => 'Head of Finance',         'department' => 'Finance',               'parent' => 'CEO'],
        ['name' => 'Head of Finance GDF',     'department' => 'Finance',               'parent' => 'CEO'],
        ['name' => 'Controller',              'department' => 'Finance',               'parent' => 'Head of Finance'],
        ['name' => 'Receivable Accountant',   'department' => 'Accounts Receivable',   'parent' => 'Head of Finance'],
        ['name' => 'Credit Manager',          'department' => 'Accounts Receivable',   'parent' => 'Head of Finance'],
        ['name' => 'Balance Accountant',      'department' => 'Accounts Payable',      'parent' => 'Head of Finance'],
        ['name' => 'Payable Accountant',      'department' => 'Accounts Payable',      'parent' => 'Head of Finance'],
        ['name' => 'Head of Domestic Sales',  'department' => 'Sales Domestic',        'parent' => 'CEO'],
        ['name' => 'Domestic Sales Manager',  'department' => 'Sales Domestic',        'parent' => 'Head of Domestic Sales'],
        ['name' => 'Domestic Team-Leader',    'department' => 'Domestic Back-Office',  'parent' => 'Domestic Sales Manager'],
        ['name' => 'Domestic Sales Clerk',    'department' => 'Domestic Invoicing',    'parent' => 'Domestic Team-Leader'],
        ['name' => 'IMPLA Sales Clerk',       'department' => 'Precious Alloys/IMPLA', 'parent' => 'Domestic Team-Leader'],
        ['name' => 'Sales Rep.',              'department' => 'Sales Reps.',           'parent' => 'Head of Domestic Sales'],
        ['name' => 'Head of Export Sales',    'department' => 'Sales Export',          'parent' => 'CEO'],
        ['name' => 'Export Controle Officer', 'department' => 'Sales Export',          'parent' => 'CEO'],
        ['name' => 'Export Sales Clerk',      'department' => 'Export Back-Office',    'parent' => 'Domestic Sales Manager'],
        ['name' => 'Area Manager',            'department' => 'Area Managers',         'parent' => 'Head of Export Sales'],
        ['name' => 'Head of Secretariat',     'department' => 'Secretariat',           'parent' => 'CEO'],
        ['name' => 'Secretary',               'department' => 'Secretariat',           'parent' => 'Head of Secretariat'],
        ['name' => 'Receptionist',            'department' => 'Reception',             'parent' => 'Head of Secretariat'],
        ['name' => 'Head of Registration',    'department' => 'Registration',          'parent' => 'Head of Secretariat'],
        ['name' => 'Registration Clerk',      'department' => 'Registration',          'parent' => 'Head of Registration'],
        ['name' => 'Head of Service',         'department' => 'Service',               'parent' => 'CEO'],
        ['name' => 'Service Employee',        'department' => 'Service',               'parent' => 'Head of Service'],
        ['name' => 'Head of Support',         'department' => 'Support',               'parent' => 'CEO'],
        ['name' => 'Support Employee',        'department' => 'Support',               'parent' => 'Head of Support'],
        ['name' => 'Head of Purchasing',      'department' => 'Purchasing',            'parent' => 'CEO'],
        ['name' => 'Back Office Purchasing',  'department' => 'Purchasing',            'parent' => 'Head of Purchasing'],
        ['name' => 'Head of Warehouse',       'department' => 'Warehouse',             'parent' => 'Head of Purchasing'],
        ['name' => 'Warehouse Employee',      'department' => 'Warehouse',             'parent' => 'Head of Warehouse'],
        ['name' => 'Head of QM',              'department' => 'QM',                    'parent' => 'CEO'],
        ['name' => 'QM Employee',             'department' => 'QM',                    'parent' => 'Head of QM'],
        ['name' => 'Head of QA',              'department' => 'QA',                    'parent' => 'CEO'],
        ['name' => 'QA Employee',             'department' => 'QA',                    'parent' => 'Head of QA'],
        ['name' => 'Head of HR',              'department' => 'HR',                    'parent' => 'CEO'],
        ['name' => 'HR Employee',             'department' => 'HR',                    'parent' => 'Head of HR'],
        ['name' => 'Head of IT',              'department' => 'IT',                    'parent' => 'CEO'],
        ['name' => 'IT Employee',             'department' => 'IT',                    'parent' => 'Head of IT'],
        ['name' => 'Head of Marketing',       'department' => 'Marketing',             'parent' => 'CEO'],
        ['name' => 'Marketing Employee',      'department' => 'Marketing',             'parent' => 'Head of Marketing'],
        ['name' => 'Trainee',                 'department' => 'HR',                    'parent' => 'Head of HR'],
    ],
];
