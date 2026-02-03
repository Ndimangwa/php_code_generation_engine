<?php
class PDFEngine extends TCPDF
{
    private $logo_file = null;
    private $headerText = null;
    private $footerText = null;
    private $fontFamily = "helvetica";
    private $cFontSize = 12;
    private $settings;
    public static function getADefaultEngine($settings = array()) {
        if (is_null($settings)) $settings = array();
        $pdf1 = new PDFEngine(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        if (isset($settings['font-family'])) $pdf1->setFontFamily($settings['font-family']);
        if (isset($settings['font-size'])) $pdf1->setCustomFontSize($settings['font-size']);
        $pdf1->setDefaultConfigurations($settings);
        $pdf1->setSettings($settings);
        return $pdf1;
    }
    //From Web
    public function setSettings($settings)  {
        $this->settings = $settings;
    }
    public function getSettings()   {
        return $this->settings;
    }
    public function setCustomFontSize($fontSize)  {
        $this->cFontSize = $fontSize;
        return $this;
    }
    public function getCustomFontSize() {
        return $this->cFontSize;
    }
    public function setFontFamily($fontFamily)  {
        $this->fontFamily = $fontFamily;
        return $this;
    }
    public function getFontFamily()
    {
        return $this->fontFamily;
    }
    public function Header()
    {
        $logo_file = $this->logo_file;
        if (!is_null($logo_file) && file_exists($logo_file)) {
            $extension = $this->getFileExtension($logo_file, array('PNG', 'JPG', 'JPEG'), 'PNG', false);
            $this->Image($logo_file, 10, 3, 25, '', $extension, '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        $this->SetFont($this->fontFamily, 'B', 20);
        $this->Cell(0, 15, '', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln();
        $headerText = is_null($this->headerText) ? "Default Text" : ($this->headerText);
        $this->Cell(0, 15, $headerText, 0, false, 'R', 0, '', 0, false, 'M', 'M');
    }
    public function Footer()
    {
        if (!is_null($this->footerText)) {
            $this->SetY(-15);
            $this->SetFont($this->fontFamily, 'I', 15);
            $this->Cell(0, 10, $this->footerText, 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }
    }
    public function printTable($header, $data)
    {
        $this->SetFillColor(0, 0, 0);
        $this->setTextColor(255);
        $this->setDrawColor(128, 0, 0);
        $this->setLineWidth(0.3);
        $this->SetFont('', 'B', 12);

        $w = array(110, 17, 25, 30);
        $num_headers = count($header);
        for ($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();
        //Color and Font Restoration
        $this->setFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');

        //$table data
        $fill = 0;
        $total = 0;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, $row[1], 'LR', 0, 'R', $fill);
            $this->Cell($w[2], 6, number_format($row[2]), 'LR', 0, 'R');
            $this->Cell($w[3], 6, number_format($row[3]), 'LR', 0, 'R');
            $this->Ln();
            $fill = !$fill;
            $total += $row[3];
        }
        $this->Cell($w[0], 6, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 6, '', 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 6, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[3], 6, '', 'LR', 0, 'R', $fill);
        $this->Ln();

        $this->Cell($w[0], 6, '', 'LR', 0, 'L', $fill);
        $this->Cell($w[1], 6, '', 'LR', 0, 'R', $fill);
        $this->Cell($w[2], 6, 'TOTAL:', 'LR', 0, 'L', $fill);
        $this->Cell($w[3], 6, $total, 'LR', 0, 'R', $fill);
        $this->Ln();

        $this->Cell(array_sum($w), 0, '', 'T');
    }
    //Tools
    private function getFileExtension($filename, $allowableExtensions = null, $defaultExtension = null, $caseSensitive = false)
    {
        $t1 = explode(".", $filename);
        $extension = $t1[sizeof($t1) - 1];
        if (!is_null($allowableExtensions)) {
            $found = in_array($extension, $allowableExtensions);
            if (!$found && !$caseSensitive) {
                foreach ($allowableExtensions as $aext) {
                    if (strtolower($extension) == strtolower($aext)) {
                        $extension = $aext; //Need to preserve the allowed one 
                        $found = true;
                        break;
                    }
                }
            }
            if (!$found) $extension = $defaultExtension;
        }
        return $extension;
    }
    //Begin Header and Setters
    public function setLogoFile($logo_file)
    {
        $this->logo_file = $logo_file;
    }
    public function getLogoFile()
    {
        return $this->logo_file;
    }
    public function getHeaderText()
    {
        return $this->headerText;
    }
    public function setHeaderText($headerText)
    {
        $this->headerText = $headerText;
    }
    public function getFooterText()
    {
        return $this->footerText;
    }
    public function setFooterText($footerText)
    {
        $this->footerText = $footerText;
    }
    //--- Up Header And Setters
    //Initialize 
    public function setDefaultConfigurations($fontConfigurations = array('font-family' => 'dejavusans', 'font-size' => 12))
    {
        if (is_null($fontConfigurations)) $fontConfigurations = array('font-family' => 'dejavusans', 'font-size' => 12);
        if (! isset($fontConfigurations['font-family'])) $fontConfigurations['font-family'] = 'dejavusans';
        if (! isset($fontConfigurations['font-size'])) $fontConfigurations['font-size'] = 12;
        $this->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->setAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->setFontSubsetting(true);
        $this->setFont($fontConfigurations['font-family'], '', $fontConfigurations['font-size'], '', true);
        return $this;
    }
    public function print($filename = null)  {
        if (is_null($filename)) $this->Output();
        else $this->Output($filename);
        return $this;
    }
}
