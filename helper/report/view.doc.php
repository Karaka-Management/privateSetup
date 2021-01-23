<?php declare(strict_types=1);
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Image;
use PhpOffice\PhpWord\Style\Language;

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

$languageEnGb = new Language(Language::EN_GB);

$phpWord = new PhpWord();
$phpWord->getSettings()->setThemeFontLang($languageEnGb);

$section = $phpWord->addSection([
    'marginTop'    => 0,
    'marginRight'  => 0,
    'marginBottom' => 0,
    'marginLeft'   => 0,
]);

/*
$section->addShape('rect', [
    'fill'      => ['color' => '#434a51'],
    'frame'     => [
        'width' => (int) Converter::cmToPoint(22),
        'height' => (int) Converter::cmToPoint(30),
        'wrappingStyle' => 'behind',
        'left' => 0, 'top' => 0
    ],
]);
*/

$section->addImage(
    __DIR__ . '/logo.png',
    [
        'width'            => (int) Converter::cmToPixel(5),
        'height'           => (int) Converter::cmToPixel(5),
        'wrappingStyle'    => 'square',
        'positioning'      => Image::POSITION_ABSOLUTE,
        'posHorizontal'    => Image::POSITION_HORIZONTAL_CENTER,
        'posHorizontalRel' => Image::POSITION_RELATIVE_TO_PAGE,
        'posVerticalRel'   => Image::POSITION_RELATIVE_TO_PAGE,
        'posVertical'      => Image::POSITION_VERTICAL_CENTER,
    ]
);

$fontStyleName = 'splashStyleF';
$phpWord->addFontStyle($fontStyleName, [
    'size' => 32, 'color' => '3697db',
]);

$paragraphStyleName = 'splashStyleP';
$phpWord->addParagraphStyle($paragraphStyleName, [
    'alignment'   => Jc::CENTER,
    'spaceBefore' => 6000,
]);

$section->addTextBox(
    [
        'width'            => (int) Converter::cmToPoint(20),
        'height'           => (int) Converter::cmToPixel(5) + 200,
        'borderSize'       => -1,
        'size'             => 32, 'color' => '3697db',
        'positioning'      => Image::POSITION_ABSOLUTE,
        'posHorizontal'    => Image::POSITION_HORIZONTAL_CENTER,
        'posHorizontalRel' => Image::POSITION_RELATIVE_TO_PAGE,
        'posVerticalRel'   => Image::POSITION_RELATIVE_TO_PAGE,
        'posVertical'      => Image::POSITION_VERTICAL_CENTER,
    ]
)->addText('Demo Report', $fontStyleName, $paragraphStyleName);

$paragraphStyleName = 'pStyle';
$phpWord->addParagraphStyle($paragraphStyleName, [
    'alignment' => Jc::CENTER, 'spaceAfter' => 100,
]);

$phpWord->addTitleStyle(1, ['bold' => true], ['spaceAfter' => 240]);

$section = $phpWord->addSection([
    'marginTop'    => 1000,
    'marginRight'  => 1000,
    'marginBottom' => 1000,
    'marginLeft'   => 1000,
]);

$titleFontStyleName = 'titleStyleF';
$phpWord->addFontStyle($titleFontStyleName, [
    'size' => 24, 'color' => '000000',
]);

$titleParagraphStyleName = 'titleStyleP';
$phpWord->addParagraphStyle($titleParagraphStyleName, [
    'spaceAfter' => 100,
]);

$section->addText('Demo Report - ' . $date->format('Y-m-d'), $titleFontStyleName, $titleParagraphStyleName);
$section->addShape('line', [
    'points'  => '0,0 ' . ((int) Converter::cmToPoint(19)) . ',0',
    'outline' => [
        'color'      => '#3697db',
        'line'       => 'thickThin',
        'weight'     => 1,
        'startArrow' => '',
        'endArrow'   => '',
    ],
]);

$section->addTextBreak(1);

$listFontStyleName = 'listStyleF';
$phpWord->addFontStyle($listFontStyleName, [
    'size' => 16, 'color' => '000000',
]);

$listParagraphStyleName = 'listStyleP';
$phpWord->addParagraphStyle($listParagraphStyleName, [
    'spaceAfter' => 300,
]);

$section->addListItem('Create custom localized reports', 0, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('They are 100% customizable in terms of style, layout and content', 0, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('You can export them as:', 0, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('Excel', 1, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('PDF', 1, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('Print', 1, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('PowerPoint', 1, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('CSV', 1, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('Word', 1, $listFontStyleName, null, $listParagraphStyleName);

$section = $phpWord->addSection([
    'marginTop'    => 1000,
    'marginRight'  => 1000,
    'marginBottom' => 1000,
    'marginLeft'   => 1000,
]);

$section->addText('Ideas for helpers', $titleFontStyleName, $titleParagraphStyleName);
$section->addShape('line', [
    'points'  => '0,0 ' . ((int) Converter::cmToPoint(19)) . ',0',
    'outline' => [
        'color'      => '#3697db',
        'line'       => 'thickThin',
        'weight'     => 1,
        'startArrow' => '',
        'endArrow'   => '',
    ],
]);

$section->addTextBreak(1);

$section->addListItem('Reports (e.g. sales, finance, marketing)', 0, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('Mailing generator based on pre-defined layouts', 0, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('Document generator based on pre-defined layouts', 0, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('Calculators (e.g. margin and price calculators)', 0, $listFontStyleName, null, $listParagraphStyleName);

$section = $phpWord->addSection([
    'marginTop'    => 1000,
    'marginRight'  => 1000,
    'marginBottom' => 1000,
    'marginLeft'   => 1000,
]);

$section->addText('Data Source', $titleFontStyleName, $titleParagraphStyleName);
$section->addShape('line', [
    'points'  => '0,0 ' . ((int) Converter::cmToPoint(19)) . ',0',
    'outline' => [
        'color'      => '#3697db',
        'line'       => 'thickThin',
        'weight'     => 1,
        'startArrow' => '',
        'endArrow'   => '',
    ],
]);

$section->addTextBreak(1);

$section->addListItem('You can provide data for the helpers in many different ways', 0, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('Manual user input', 1, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('File upload (e.g. excel, csv)', 1, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('Database upload (e.g. sqlite)', 1, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('Database connection to local or remote database', 1, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('External APIs', 1, $listFontStyleName, null, $listParagraphStyleName);
$section->addListItem('Internal APIs (everything from the Orange Management backend)', 1, $listFontStyleName, null, $listParagraphStyleName);

$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save('php://output');
