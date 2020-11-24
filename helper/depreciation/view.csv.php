<?php declare(strict_types=1);

use phpOMS\Business\Finance\Depreciation;

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

$depreciation = [
    [
        $lang['Period'],
        $lang['StraightLine'],
        $lang['ArithmeticDegressive'],
        $lang['ArithmeticProgressive'],
        $lang['GeometricDegressive'],
        $lang['GeometricProgressive'],
    ],
];

for ($i = 1; $i <= $duration; ++$i) {
    $depreciation[] = [
        $i,
        $this->getCurrency(Depreciation::getStraightLineResidualInT($amount, $duration, $i), 'medium', ''),
        $this->getCurrency(Depreciation::getArithmeticDegressiveDepreciationResidualInT($amount, 0.0, $duration, $i), 'medium', ''),
        $this->getCurrency(Depreciation::getArithmeticProgressiveDepreciationResidualInT($amount, 0.0, $duration, $i), 'medium', ''),
        $this->getCurrency(Depreciation::getGeometicProgressiveDepreciationResidualInT($amount, 0.0, $duration, $i), 'medium', ''),
        $this->getCurrency(Depreciation::getGeometicDegressiveDepreciationResidualInT($amount, $amount * 0.1, $duration, $i), 'medium', ''),
    ];
}

$out = \fopen('php://output', 'w');
foreach ($depreciation as $d) {
    \fputcsv($out, $d);
}
\fclose($out);
