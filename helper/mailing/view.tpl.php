<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Template
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

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
?>
<!DOCTYPE HTML>
<html lang="<?= $cLang; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="<?= UriFactory::build('{/base}/' . \ltrim($tcoll['css']['styles.css']->getPath(), '/')); ?>">
</head>
<body>
<?php if ($this->request->getData('type') !== 'html') : ?>
<h1>Demo Mailing</h1>

<label for="updates">Update</label>
<textarea id="updates" name="updates" form="iUiSettings"><?php
echo \str_replace(["\n"], ['&#13;&#10;'],
"<ul>
    <li>Our customers became even more awesome</li>
    <li>Our partners are the best in the world</li>
    <li>We love our employees</li>
</ul>"); ?></textarea>

<label for="goals">Goals</label>
<textarea id="goals" name="goals" form="iUiSettings"><?php
echo \str_replace(["\n"], ['&#13;&#10;'],
"<ul>
    <li>Become even better</li>
    <li>Make our customers even more happy</li>
    <li>Having a nice barbecue in the office</li>
    <li>Hopefully, not burn down the office during the barbecue</li>
</ul>"); ?></textarea>
<?php else: ?>
<div id="bar"></div>
<header>
    <h1>Demo Mailing - <?= $this->request->getData('date') ?? 'Y-m-d'; ?></h1>
    <img alt="Company Logo" src="<?= UriFactory::build('{/base}/' . \ltrim($tcoll['other']['logo.png']->getPath(), '/')); ?>">
</div>
</header>
<main>
<ul>
    <li>Updates:
        <?= \str_replace(['&#13;&#10;'], ["\n"], ($this->request->getData('updates') ?? '')); ?>
    </li>
</ul>

<ul>
    <li>Goals:
        <?= \str_replace(['&#13;&#10;'], ["\n"], ($this->request->getData('goals') ?? '')); ?>
    </li>
</ul>
</main>
<footer>
    <ul>
        <li>Website: karaka.app</li>
        <li>Email: dennis.eichhorn@karaka.email</li>
        <li>Twitter: @orange_mgmt</li>
        <li>Twitch: spl1nes</li>
        <li>Youtube: Karaka</li>
    </ul>
</footer>
<?php endif; ?>