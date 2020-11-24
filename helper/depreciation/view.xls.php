<?php declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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

$spreadsheet = new Spreadsheet();

$spreadsheet->getProperties()->setCreator('Orange Management')
    ->setLastModifiedBy('Orange Management')
    ->setTitle('Orange Management - Depreciation Demo')
    ->setSubject('Orange Management - Depreciation Demo')
    ->setDescription('Demo')
    ->setKeywords('demo helper depreciation')
    ->setCategory('demo');

$spreadsheet->setActiveSheetIndex(0)
    ->setCellValue('A1',$lang['Period'])
    ->setCellValue('B1', $lang['StraightLine'])
    ->setCellValue('C1', $lang['ArithmeticDegressive'])
    ->setCellValue('D1', $lang['ArithmeticProgressive'])
    ->setCellValue('E1', $lang['GeometricDegressive'])
    ->setCellValue('F1', $lang['GeometricProgressive']);

for ($i = 1; $i <= $duration; ++$i) {
    $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A' . ($i + 1), $i)
        ->setCellValue('B' . ($i + 1), $this->getCurrency(Depreciation::getStraightLineResidualInT($amount, $duration, $i), 'medium', ''))
        ->setCellValue('C' . ($i + 1), $this->getCurrency(Depreciation::getArithmeticDegressiveDepreciationResidualInT($amount, 0.0, $duration, $i), 'medium', ''))
        ->setCellValue('D' . ($i + 1), $this->getCurrency(Depreciation::getArithmeticProgressiveDepreciationResidualInT($amount, 0.0, $duration, $i), 'medium', ''))
        ->setCellValue('E' . ($i + 1), $this->getCurrency(Depreciation::getGeometicProgressiveDepreciationResidualInT($amount, $amount * 0.1, $duration, $i), 'medium', ''))
        ->setCellValue('F' . ($i + 1), $this->getCurrency(Depreciation::getGeometicDegressiveDepreciationResidualInT($amount, $amount * 0.1, $duration, $i), 'medium', ''));
}

$spreadsheet->getActiveSheet()->setTitle($lang['Depreciation']);
$spreadsheet->setActiveSheetIndex(0);

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
