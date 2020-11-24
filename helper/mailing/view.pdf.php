<?php declare(strict_types=1);

use phpOMS\Autoloader;

require_once Autoloader::findPaths('Resources\tcpdf\tcpdf')[0];

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator('Dennis Eichhorn');
$pdf->SetAuthor('Dennis Eichhorn');
$pdf->SetTitle('Demo Mailing');
$pdf->SetSubject('Mailing');
$pdf->SetKeywords('demo helper mailing');

$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(false, 0);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

$pdf->SetFillColor(52, 58, 64);
$pdf->Rect(0, 0, $pdf->getPageWidth(), $pdf->getPageHeight(), 'F');

$pdf->SetFillColor(54, 151, 219);
$pdf->Rect(0, 0, $pdf->getPageWidth(), 5, 'F');

$pdf->SetFont('helvetica', '', 32);
$pdf->SetTextColor(54, 151, 219);
$pdf->Write(0, 'Demo Mailing - ' . $this->request->getData('date') ?? 'Y-m-d', '', 0, 'C', true, 0, false, false, 0);

$pdf->Image(__DIR__ . '/logo.png', $pdf->getPageWidth() / 2 - 60 / 2, 40, 60, 60, 'PNG', '', 'C', true, 300, '', false, false, 0, false, false, false);

$pdf->SetFillColor(67, 74, 81);
$pdf->Rect(0, 110, $pdf->getPageWidth(), 145, 'F');

$pdf->setListIndentWidth(10);
$html = '<ul>
    <li>Updates:
        ' . \str_replace(['&#13;&#10;'], ["\n"], ($this->request->getData('updates') ?? '')) . '
    </li>
</ul>';

$pdf->SetXY(15, 125);
$pdf->SetFont('helvetica', '', 21);
$pdf->SetTextColor(255, 255, 255);
$pdf->writeHTML($html, true, false, true, false, '');

$html = '<ul>
    <li>Goals:
    ' . \str_replace(['&#13;&#10;'], ["\n"], ($this->request->getData('goals') ?? '')) . '
    </li>
</ul>';

$pdf->SetXY(15, 180);
$pdf->SetFont('helvetica', '', 21);
$pdf->SetTextColor(255, 255, 255);
$pdf->writeHTML($html, true, false, true, false, '');

$pdf->SetFont('helvetica', '', 12);
$pdf->SetXY(15, 262);
$pdf->SetTextColor(54, 151, 219);

$text = <<<EOT
Website: orange-management.org
Email: dennis.eichhorn@orange-management.email
Twitter: @orange_mgmt
Twitch: spl1nes
Youtube: Orange-Management
EOT;
$pdf->Write(0, $text, '', 0, 'L', true, 0, false, false, 0);

$pdf->Output('mailing.pdf', 'I');
