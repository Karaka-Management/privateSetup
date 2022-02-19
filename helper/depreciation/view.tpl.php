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

use phpOMS\Business\Finance\Depreciation;
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

$amount   = (float) ($this->request->getData('amount') ?? 10000.0);
$duration = (int) ($this->request->getData('duration') ?? 10);

?>
<!DOCTYPE HTML>
<html lang="<?= $cLang; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="<?= UriFactory::build('{/base}/' . \ltrim($tcoll['css']['styles.css']->getPath(), '/')); ?>">
</head>
<body>
<table>
    <caption><?= $lang['Depreciation']; ?></caption>
    <thead>
        <tr>
            <td><?= $lang['Period']; ?></td>
            <td><?= $lang['StraightLine']; ?></td>
            <td><?= $lang['ArithmeticDegressive']; ?></td>
            <td><?= $lang['ArithmeticProgressive']; ?></td>
            <td><?= $lang['GeometricDegressive']; ?></td>
            <td><?= $lang['GeometricProgressive']; ?></td>
        </tr>
    </thead>
    <tbody>
    <?php for ($i = 1; $i <= $duration; ++$i) : ?>
        <tr>
            <td><?= $i; ?></td>
            <td><?=  $this->getCurrency(Depreciation::getStraightLineResidualInT($amount, $duration, $i), 'medium', ''); ?></td>
            <td><?=  $this->getCurrency(Depreciation::getArithmeticDegressiveDepreciationResidualInT($amount, 0.0, $duration, $i), 'medium', ''); ?></td>
            <td><?=  $this->getCurrency(Depreciation::getArithmeticProgressiveDepreciationResidualInT($amount, 0.0, $duration, $i), 'medium', ''); ?></td>
            <td><?=  $this->getCurrency(Depreciation::getGeometicProgressiveDepreciationResidualInT($amount, $amount * 0.1, $duration, $i), 'medium', ''); ?></td>
            <td><?=  $this->getCurrency(Depreciation::getGeometicDegressiveDepreciationResidualInT($amount, $amount * 0.1, $duration, $i), 'medium', ''); ?></td>
        </tr>
    <?php endfor; ?>
    </tbody>
</table>

<blockquote><?= $lang['info']; ?></blockquote>
