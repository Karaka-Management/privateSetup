<?php

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
?>
<!DOCTYPE HTML>
<html lang="<?= $cLang; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="<?= UriFactory::build('{/base}/' . \ltrim($tcoll['css']['styles']->getPath(), '/')); ?>">
</head>
<body>
<?php if ($this->request->getData('type') === 'html') : ?>
<div class="splash">
<img alt="Company Logo" src="<?= UriFactory::build('{/base}/' . \ltrim($tcoll['other']['logo']->getPath(), '/')); ?>">
<h1>Demo Report</h1>
</div>
<?php endif; ?>

<div class="slide">
<h1>Demo Report - <?= $date->format('Y-m-d'); ?></h1>

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

<div class="footer"><img alt="Company Logo" src="<?= UriFactory::build('{/base}/' . \ltrim($tcoll['other']['logo']->getPath(), '/')); ?>"></div>
</div>

<div class="slide">
<h1>Ideas for helpers</h1>

<ul>
    <li>Reports (e.g. sales, finance, marketing</li>
    <li>Mailing generator based on pre-defined layouts</li>
    <li>Document generator based on pre-defined layouts</li>
    <li>Calculators (e.g. margin and price calculators)</li>
</ul>

<div class="footer"><img alt="Company Logo" src="<?= UriFactory::build('{/base}/' . \ltrim($tcoll['other']['logo']->getPath(), '/')); ?>"></div>
</div>

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

<div class="footer"><img alt="Company Logo" src="<?= UriFactory::build('{/base}/' . \ltrim($tcoll['other']['logo']->getPath(), '/')); ?>"></div>
</div>

<?php if ($this->request->getData('type') === 'html') : ?>
<div class="splash">
<img alt="Company Logo" src="<?= UriFactory::build('{/base}/' . \ltrim($tcoll['other']['logo']->getPath(), '/')); ?>">
<h1>Thank you!</h1>
</div>
<?php endif; ?>