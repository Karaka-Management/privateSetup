<?php declare(strict_types=1);

use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Drawing\File;
use PhpOffice\PhpPresentation\Shape\Line;
use PhpOffice\PhpPresentation\Slide\Background\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Color as StyleColor;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

$objPHPPresentation = new PhpPresentation();

$objPHPPresentation->getDocumentProperties()->setCreator('Karaka')
    ->setLastModifiedBy('Karaka')
    ->setTitle('Karaka - Demo Report')
    ->setSubject('Karaka - Demo Report')
    ->setDescription('Demo')
    ->setKeywords('demo helper report')
    ->setCategory('demo');

$colorBlack = new StyleColor('FF000000');
$colorBlue  = new StyleColor('FF3697db');
$colorDark  = new StyleColor('FF434a51');

// start screen
$oSlide1   = $objPHPPresentation->getActiveSlide();
$oBkgColor = new Color();
$oBkgColor->setColor($colorDark);
$oSlide1->setBackground($oBkgColor);

$shape = new File();
$shape->setName('Company Logo')
	->setDescription('Company Logo')
	->setPath(__DIR__ . '/logo.png')
	->setHeight(300)
	->setOffsetX(320)
    ->setOffsetY(120);
$oSlide1->addShape($shape);

$shape = $oSlide1->createRichTextShape()
    ->setHeight(300)
    ->setWidth(600)
    ->setOffsetX(180)
    ->setOffsetY(450);
$shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$textRun = $shape->createTextRun('Demo Report');
$textRun->getFont()->setBold(true)->setSize(35)->setColor($colorBlue);

// first slide
$oSlide2 = $objPHPPresentation->createSlide();

$shape = $oSlide2->createRichTextShape()
    ->setHeight(25)
    ->setWidth(960)
    ->setOffsetX(0)
    ->setOffsetY(0);

$shape->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->setStartColor($colorBlue)
    ->setEndColor($colorBlue);

$shape = $oSlide2->createRichTextShape()
    ->setHeight(75)
    ->setWidth(860)
    ->setOffsetX(50)
    ->setOffsetY(65);

$shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$textRun = $shape->createTextRun('Demo Report - ' . $date->format('Y-m-d'));
$textRun->getFont()->setBold(false)->setSize(35)->setColor($colorBlack);

$line = new Line(50, 130, 910, 130);
$line->getBorder()->setColor($colorBlue);
$oSlide2->addShape($line);

$shape = $oSlide2->createRichTextShape();
$shape->setHeight(600)
      ->setWidth(930)
      ->setOffsetX(10)
      ->setOffsetY(170);

$shape->getActiveParagraph()
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
    ->setMarginLeft(50)
    ->setIndent(-25);

$shape->getActiveParagraph()->getFont()->setSize(21)->setColor($colorBlack);
$shape->getActiveParagraph()->getBulletStyle()->setBulletType(Bullet::TYPE_BULLET)->setBulletChar('•');

$shape->createTextRun('Create custom localized reports');
$shape->createParagraph()
    ->getAlignment()
    ->setLevel(1)
    ->setMarginLeft(150)
    ->setIndent(-25);

$shape->createParagraph()->getAlignment()->setLevel(0)->setMarginLeft(50)->setIndent(-25);
$shape->createParagraph()->getAlignment()->setLevel(0)->setMarginLeft(50)->setIndent(-25);

$shape->createTextRun('They are 100% customizable in terms of style, layout and content');
$shape->createParagraph()
    ->getAlignment()
    ->setLevel(1)
    ->setMarginLeft(150)
    ->setIndent(-25);

$shape->createParagraph()->getAlignment()->setLevel(0)->setMarginLeft(50)->setIndent(-25);
$shape->createParagraph()->getAlignment()->setLevel(0)->setMarginLeft(50)->setIndent(-25);

$shape->createTextRun('You can export them as:');
$shape->createParagraph()
    ->getAlignment()
    ->setLevel(1)
    ->setMarginLeft(150)
    ->setIndent(-25);

$shape->getActiveParagraph()->getBulletStyle()->setBulletType(Bullet::TYPE_BULLET)->setBulletChar('◦');
$shape->createTextRun('Excel');
$shape->createParagraph()->createTextRun('PDF');
$shape->createParagraph()->createTextRun('Print');
$shape->createParagraph()->createTextRun('PowerPoint');
$shape->createParagraph()->createTextRun('CSV');
$shape->createParagraph()->createTextRun('Word');

$shape = new File();
$shape->setName('Company Logo')
	->setDescription('Company Logo')
	->setPath(__DIR__ . '/logo.png')
	->setHeight(50)
	->setOffsetX(880)
    ->setOffsetY(650);
$oSlide2->addShape($shape);

// second slide
$oSlide3 = $objPHPPresentation->createSlide();

$shape = $oSlide3->createRichTextShape()
    ->setHeight(25)
    ->setWidth(960)
    ->setOffsetX(0)
    ->setOffsetY(0);

$shape->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->setStartColor($colorBlue)
    ->setEndColor($colorBlue);

$shape = $oSlide3->createRichTextShape()
    ->setHeight(75)
    ->setWidth(860)
    ->setOffsetX(50)
    ->setOffsetY(65);

$shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$textRun = $shape->createTextRun('Ideas for helpers');
$textRun->getFont()->setBold(false)->setSize(35)->setColor($colorBlack);

$line = new Line(50, 130, 910, 130);
$line->getBorder()->setColor($colorBlue);
$oSlide3->addShape($line);

$shape = $oSlide3->createRichTextShape();
$shape->setHeight(600)
      ->setWidth(930)
      ->setOffsetX(10)
      ->setOffsetY(170);

$shape->getActiveParagraph()
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
    ->setMarginLeft(50)
    ->setIndent(-25);

$shape->getActiveParagraph()->getFont()->setSize(21)->setColor($colorBlack);
$shape->getActiveParagraph()->getBulletStyle()->setBulletType(Bullet::TYPE_BULLET)->setBulletChar('•');

$shape->createTextRun('Reports (e.g. sales, finance marketing)');
$shape->createParagraph()
    ->getAlignment()
    ->setLevel(1)
    ->setMarginLeft(150)
    ->setIndent(-25);

$shape->createParagraph()->getAlignment()->setLevel(0)->setMarginLeft(50)->setIndent(-25);
$shape->createParagraph()->getAlignment()->setLevel(0)->setMarginLeft(50)->setIndent(-25);

$shape->createTextRun('Mailing generator based on pre-defined layouts');
$shape->createParagraph()
    ->getAlignment()
    ->setLevel(1)
    ->setMarginLeft(150)
    ->setIndent(-25);

$shape->createParagraph()->getAlignment()->setLevel(0)->setMarginLeft(50)->setIndent(-25);
$shape->createParagraph()->getAlignment()->setLevel(0)->setMarginLeft(50)->setIndent(-25);

$shape->createTextRun('Document generator based on pre-defined layouts');
$shape->createParagraph()
    ->getAlignment()
    ->setLevel(1)
    ->setMarginLeft(150)
    ->setIndent(-25);

$shape->createParagraph()->getAlignment()->setLevel(0)->setMarginLeft(50)->setIndent(-25);
$shape->createParagraph()->getAlignment()->setLevel(0)->setMarginLeft(50)->setIndent(-25);

$shape->createTextRun('Calculators (e.g. margin calculator)');
$shape->createParagraph()
    ->getAlignment()
    ->setLevel(1)
    ->setMarginLeft(150)
    ->setIndent(-25);

$shape = new File();
$shape->setName('Company Logo')
	->setDescription('Company Logo')
	->setPath(__DIR__ . '/logo.png')
	->setHeight(50)
	->setOffsetX(880)
    ->setOffsetY(650);
$oSlide3->addShape($shape);

// third slide
$oSlide4 = $objPHPPresentation->createSlide();

$shape = $oSlide4->createRichTextShape()
    ->setHeight(25)
    ->setWidth(960)
    ->setOffsetX(0)
    ->setOffsetY(0);

$shape->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->setStartColor($colorBlue)
    ->setEndColor($colorBlue);

$shape = $oSlide4->createRichTextShape()
    ->setHeight(75)
    ->setWidth(860)
    ->setOffsetX(50)
    ->setOffsetY(65);

$shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$textRun = $shape->createTextRun('Data Source');
$textRun->getFont()->setBold(false)->setSize(35)->setColor($colorBlack);

$line = new Line(50, 130, 910, 130);
$line->getBorder()->setColor($colorBlue);
$oSlide4->addShape($line);

$shape = $oSlide4->createRichTextShape();
$shape->setHeight(600)
      ->setWidth(930)
      ->setOffsetX(10)
      ->setOffsetY(170);

$shape->getActiveParagraph()
    ->getAlignment()
    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
    ->setMarginLeft(50)
    ->setIndent(-25);

$shape->getActiveParagraph()->getFont()->setSize(21)->setColor($colorBlack);
$shape->getActiveParagraph()->getBulletStyle()->setBulletType(Bullet::TYPE_BULLET)->setBulletChar('•');

$shape->createTextRun('You can provide data for the helpers in many different ways');
$shape->createParagraph()
    ->getAlignment()
    ->setLevel(1)
    ->setMarginLeft(150)
    ->setIndent(-25);

$shape->getActiveParagraph()->getBulletStyle()->setBulletType(Bullet::TYPE_BULLET)->setBulletChar('◦');
$shape->createTextRun('Manual user input');
$shape->createParagraph()->createTextRun('File upload (e.g. excel, csv)');
$shape->createParagraph()->createTextRun('Database upload (e.g. sqlite)');
$shape->createParagraph()->createTextRun('Database connection to local or remote database');
$shape->createParagraph()->createTextRun('External APIs');
$shape->createParagraph()->createTextRun('Internal APIs (everything from the Karaka backend)');

$shape = new File();
$shape->setName('Company Logo')
	->setDescription('Company Logo')
	->setPath(__DIR__ . '/logo.png')
	->setHeight(50)
	->setOffsetX(880)
    ->setOffsetY(650);
$oSlide4->addShape($shape);

// end screen
$oSlide5 = $objPHPPresentation->createSlide();
$oSlide5->setBackground($oBkgColor);

$shape = new File();
$shape->setName('Company Logo')
	->setDescription('Company Logo')
	->setPath(__DIR__ . '/logo.png')
	->setHeight(300)
	->setOffsetX(320)
    ->setOffsetY(120);
$oSlide5->addShape($shape);

$shape = $oSlide5->createRichTextShape()
    ->setHeight(300)
    ->setWidth(600)
    ->setOffsetX(180)
    ->setOffsetY(450);
$shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$textRun = $shape->createTextRun('Thank You!');
$textRun->getFont()->setBold(true)->setSize(42)->setColor($colorBlue);

$writer = IOFactory::createWriter($objPHPPresentation, 'PowerPoint2007');
$writer->save('php://output');
