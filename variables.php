<?php declare(strict_types=1);

use phpOMS\Account\AccountStatus;
use phpOMS\Localization\ISO639x1Enum;

return [
    'languages' => [
        ISO639x1Enum::_EN, // English
        ISO639x1Enum::_DE, // German
    ],
    'mFiles' => [],
    'accounts' => [
        [
            'login'  => 'spl1nes',
            'pass'   => 'orange',
            'name1'  => 'Dennis',
            'name2'  => 'Eichhorn',
            'image'  => 'profile.jpg',
            'status' => AccountStatus::ACTIVE,
            'email'  => 'info@jingga.app',
            'groups' => ['user', 'org:dep:management'],
        ],
    ],
    'departments' => [
        ['name' => 'Management',         'parent' => null],
        ['name' => 'Sales',              'parent' => 'Management'],
        ['name' => 'Marketing',          'parent' => 'Management'],
        ['name' => 'Finance',            'parent' => 'Management'],
        ['name' => 'HR',                 'parent' => 'Management'],
        ['name' => 'Procurement',        'parent' => 'Management'],
        ['name' => 'Development',        'parent' => 'Management'],
        ['name' => 'Support & Service',  'parent' => 'Management'],
        ['name' => 'IT',                 'parent' => 'Management'],
        ['name' => 'Quality Management', 'parent' => 'Management'],
    ],
    'positions' => [
        ['name' => 'CEO',              'department' => 'Management',  'parent' => null],
        ['name' => 'Executive Member', 'department' => null,          'parent' => 'CEO'],
        ['name' => 'COO',              'department' => 'Management',  'parent' => 'CEO'],
        ['name' => 'CTO',              'department' => 'Development', 'parent' => 'CEO'],
        ['name' => 'CFO',              'department' => 'Finance',     'parent' => 'CEO'],
        ['name' => 'CSO',              'department' => 'Sales',       'parent' => 'CEO'],
        ['name' => 'HOM',              'department' => 'Marketing',   'parent' => 'CEO'],
        ['name' => 'Head of IT',       'department' => 'IT',          'parent' => 'CTO'],
        ['name' => 'Controller',       'department' => 'Finance',     'parent' => 'CFO'],
        ['name' => 'Accountant',       'department' => 'Finance',     'parent' => 'CFO'],
        ['name' => 'Developer',        'department' => 'Development', 'parent' => 'CTO'],
    ],
];
