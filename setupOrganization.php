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

use Modules\Organization\Models\DepartmentMapper;
use Modules\Organization\Models\Status;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\System\MimeType;
use phpOMS\Uri\HttpUri;
use phpOMS\Utils\TestUtils;

/**
 * Setup departments
 *
 * @var \Modules\Organization\Controller\ApiController $module
 */
//region Department
/** @var \phpOMS\Application\ApplicationAbstract $app */
$module = $app->moduleManager->get('Organization');
TestUtils::setMember($module, 'app', $app);

$departments = [
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
];

$departmentIds = [];

foreach ($departments as $department) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->getHeader()->setAccount(1);
    $request->setData('name', $department['name']);
    $request->setData('status', Status::ACTIVE);
    $request->setData('unit', 2);
    $request->setData('parent', $departmentIds[$department['parent']] ?? null);
    $request->setData('description', \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6'));

    $module->apiDepartmentCreate($request, $response);
    $departmentIds[$department['name']] = $response->get('')['response']->getId();
}
//endregion

/**
 * Setup positions
 *
 * @var \Modules\Organization\Controller\ApiController $module
 */
//region Departments
$module = $app->moduleManager->get('Organization');
TestUtils::setMember($module, 'app', $app);

$positions = [
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
];

$departments = DepartmentMapper::getAll();
$postionIds  = [];
foreach ($positions as $position) {
    $response = new HttpResponse();
    $request  = new HttpRequest(new HttpUri(''));

    $request->getHeader()->setAccount(1);
    $request->setData('name', $position['name']);
    $request->setData('status', Status::ACTIVE);
    $request->setData('parent', $positionIds[$position['parent']] ?? null);
    $request->setData('description', \file_get_contents(__DIR__ . '/lorem_ipsum/' . \mt_rand(0, 999) . '_3-6'));

    foreach ($departments as $d) {
        if (!isset($position['department']) || $d->getName() === $position['department']) {
            $request->setData('department', $d->getId());
            $module->apiPositionCreate($request, $response);

            $positionIds[$position['name']] = $response->get('')['response']->getId();
            break;
        }
    }
}
//endregion

//region Organization image
if (!\file_exists(__DIR__ . '/temp')) {
    \mkdir(__DIR__ . '/temp');
}

// upload orange management icon
\copy(__DIR__ . '/img/m_icon.png', __DIR__ . '/temp/m_icon.png');

$module = $app->moduleManager->get('Organization');
TestUtils::setMember($module, 'app', $app);

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->getHeader()->setAccount(1);
$request->setData('name', 'Orange Management Logo');
$request->setData('id', 1);

TestUtils::setMember($request, 'files', [
    'file1' => [
        'name'     => 'm_icon.png',
        'type'     => MimeType::M_PNG,
        'tmp_name' => __DIR__ . '/temp/m_icon.png',
        'error'    => \UPLOAD_ERR_OK,
        'size'     => \filesize(__DIR__ . '/img/m_icon.png'),
    ],
]);
$module->apiUnitImageSet($request, $response);

// upload lima icon
\copy(__DIR__ . '/img/m_icon.png', __DIR__ . '/temp/m_icon.png');

$response = new HttpResponse();
$request  = new HttpRequest(new HttpUri(''));

$request->getHeader()->setAccount(1);
$request->setData('name', 'Lima Logo');
$request->setData('id', 2);

TestUtils::setMember($request, 'files', [
    'file1' => [
        'name'     => 'm_icon.png',
        'type'     => MimeType::M_PNG,
        'tmp_name' => __DIR__ . '/temp/m_icon.png',
        'error'    => \UPLOAD_ERR_OK,
        'size'     => \filesize(__DIR__ . '/img/m_icon.png'),
    ],
]);
$module->apiUnitImageSet($request, $response);
//endregion
