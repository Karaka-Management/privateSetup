<?php

use Mpdf\Mpdf;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View $this
 */
$tcoll    = $this->getData('tcoll');
$rcoll    = $this->getData('rcoll');
$cLang    = $this->getData('lang');
$template = $this->getData('template');
$report   = $this->getData('report');
$basepath = \rtrim($this->getData('basepath') ?? '', '/');

/** @noinspection PhpIncludeInspection */
$reportLanguage = include $basepath . '/' . \ltrim($tcoll['lang']->getPath(), '/');
$lang           = $reportLanguage[$cLang];

$date = new \phpOMS\Stdlib\Base\SmartDateTime($this->request->getData('date') ?? 'Y-m-d');

$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4-L',
    'orientation' => 'L',
    'margin_left' => 0,
	'margin_right' => 0,
	'margin_top' => 0,
	'margin_bottom' => 0,
	'margin_header' => 0,
	'margin_footer' => 0
]);

$mpdf->SetDisplayMode('fullpage');
$mpdf->SetTitle("Orange Management - Demo Report");
$mpdf->SetAuthor("Orange Management");

// Write some HTML code:
$mpdf->WriteHTML('
<html>
<head>
    <style>
        body {
            width: 100%;
            height: 100%;
            min-width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        h1 {
            border-bottom: 1px solid #3697db;
            font-weight: 100;
            font-size: 1.5rem;
            padding-bottom: .2rem;
        }

        ul {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        li {
            margin: 1rem 0 1rem 0;
        }

        .splash {
            width: 100%;
            height: 100%;
            background: #434a51;
            display: block;
            text-align: center;
        }

        .splash-img {
            margin-top: 180px;
            width: 300px;
            height: 300px;
        }

        .splash h1 {
            border: none;
            font-size: 4rem;
            color: #3697db;
        }

        .slide {
            padding: 30px 50px 30px 50px;
            height: 100%;
            border-top: 15px solid #3697db;
        }

        .footer-img {
            width: 100px;
            height: 100px;
            float: right;
            margin: 10px;
        }
    </style>
    </head>
<body>

<div class="splash">
    <img alt="Company Logo" class="splash-img" src="' . UriFactory::build('{/base}/' . \ltrim($tcoll['other']['logo']->getPath(), '/')) . '">
    <h1>Demo Report</h1>
</div>
');

$mpdf->AddPage();

$mpdf->SetHTMLFooter('
<div class="footer">
    <img alt="Company Logo" class="footer-img" src="' . UriFactory::build('{/base}/' . \ltrim($tcoll['other']['logo']->getPath(), '/')) . '">
</div>
');

$mpdf->WriteHTML('
<div class="slide">
    <h1>Demo Report - ' . $date->format('Y-m-d') . '</h1>

    <ul>
        <li>Create custom localized reports</li>
        <li>They are 100% customizable in terms of style, layout and content</li>
        <li>You can export them as:
            <ul>
                <li>Excel</li>
                <li>PDF</li>
                <li>Print</li>
                <li>PowerPoint</li>
                <li>CSV</li>
                <li>Word</li>
            </ul>
        </li>
    </ul>
</div>
');

$mpdf->AddPage();

$mpdf->WriteHTML('
<div class="slide">
    <h1>Ideas for helpers</h1>

    <ul>
        <li>Reports (e.g. sales, finance, marketing</li>
        <li>Mailing generator based on pre-defined layouts</li>
        <li>Document generator based on pre-defined layouts</li>
        <li>Calculators (e.g. margin and price calculators)</li>
    </ul>
</div>
');

$mpdf->AddPage();

$mpdf->WriteHTML('
<div class="slide">
    <h1>Data Source</h1>

    <ul>
        <li>You can provide data for the helpers in many different ways
            <ul>
                <li>Manual user input</li>
                <li>File upload (e.g. excel, csv)</li>
                <li>Database upload (e.g. sqlite)</li>
                <li>Database connection to local or remote database</li>
                <li>External APIs</li>
                <li>Internal APIs (everything from the Orange Management backend)</li>
            </ul>
        </li>
    </ul>
</div>
');

$mpdf->AddPage();

$mpdf->SetHTMLFooter('');

$mpdf->WriteHTML('
<div class="splash">
    <img alt="Company Logo" class="splash-img" src="' . UriFactory::build('{/base}/' . \ltrim($tcoll['other']['logo']->getPath(), '/')) . '">
    <h1>Thank you!</h1>
</div>
');

$mpdf->Output();
