<?php
class PDFLayout
{
    public static $__NEW_LINE = "-a983_98_";
    public static function writeTable($pdf1, $dataBlock1, $header1, $columnratio = null, $maxwidth = 180, $settings = null)
    {
        //$settings = is_null($settings) ? array() : $settings;
        $settings = __data__::combineArray($settings, $pdf1->getSettings());
        $settings = is_null($settings) ? array() : $settings;
        $fontSize = isset($settings['font-size']) ? $settings['font-size'] : 12;
        /*dataBlock1[i]['format' => array('align:0' => 'R'), 'data' => array()] 
        
        */
        if (is_null($header1)) return $pdf1;
        $headerLength = sizeof($header1);
        //The controlling factor is header1 
        if (is_null($columnratio)) $columnratio = array();
        $totalRatio = 0;
        for ($i = 0; $i < $headerLength; $i++) {
            if (!isset($columnratio[$i])) $columnratio[$i] = strlen($header1[$i]);
            $totalRatio += $columnratio[$i];
        }
        if ($totalRatio == 0) throw new Exception("Ratio: Division by zero");
        $actualTableWidth = 0;
        for ($i = 0; $i < $headerLength; $i++) {
            $columnratio[$i] = floor(($columnratio[$i] * $maxwidth) / $totalRatio);
            $actualTableWidth += $columnratio[$i];
        }
        //Now we proceed 
        $pdf1->SetFillColor(0, 0, 0);
        $pdf1->setTextColor(255);
        $pdf1->setDrawColor(128, 0, 0);
        $pdf1->setLineWidth(0.3);
        $pdf1->SetFont('', 'B', $fontSize);

        $num_headers = count($header1);
        for ($i = 0; $i < $num_headers; ++$i) {
            $pdf1->Cell($columnratio[$i], 7, $header1[$i], 1, 0, 'C', 1);
        }
        $pdf1->Ln();
        //Color and Font Restoration
        $pdf1->setFillColor(224, 235, 255);
        $pdf1->SetTextColor(0);
        $pdf1->SetFont('');
        //$table data
        $fill = false;
        foreach ($dataBlock1 as $row) {
            if ($row == (self::$__NEW_LINE)) {
                for ($i = 0; $i < $headerLength; $i++) {
                    $pdf1->Cell($columnratio[$i], 6, '', 'LR', 0, 'L', $fill);
                }
                $fill = !$fill;
                $pdf1->Ln();
                continue;
            }
            $format = array();
            if (isset($row['format'])) {
                foreach ($row['format'] as $tformat => $value) {
                    $t1 = explode(":", $tformat);
                    if (sizeof($t1) != 2) continue;
                    $key = $t1[0];
                    $index = $t1[1];
                    if (in_array($key, array("align", "format_number", "fill", "colspan"))) {
                        if (!isset($fotmat[$index])) $format[$index] = array();
                        $format[$index][$key] = $value;
                    }
                }
            }
            //Now working with data
            if (isset($row['data'])) {
                for ($i = 0; $i < sizeof($row['data']); $i++) {
                    if ($i >= $headerLength) break; //We can not exceed header-length
                    $value = $row['data'][$i];
                    $tformat = isset($format[$i]) ? $format[$i] : null;
                    if (is_null($tformat)) {
                        $pdf1->Cell($columnratio[$i], 6, $value, 'LR', 0, 'L', $fill);
                    } else {
                        $value = isset($tformat['number_format']) ? number_format($value) : $value;
                        $align = isset($tformat['align']) ? $tformat['align'] : 'L';
                        $tfill = isset($tformat['fill']) ? ($fill && $tformat['fill']) : $fill;
                        $colspan = isset($tformat['colspan']) ? $tformat['colspan'] : 1;
                        //Working with colspan
                        $cratio = $columnratio[$i];
                        $limit = $i + $colspan;
                        if ($limit > $headerLength) {
                            $limit = $headerLength;
                        }
                        for ($j = $i + 1; $j < $limit; $j++) {
                            $cratio += $columnratio[$j];
                        }
                        $i = $limit - 1;  //for-loop will compasate the -1
                        $mystyle = ($colspan > 1) ? 'LRTB' : 'LR';
                        $pdf1->Cell($cratio, 6, $value, $mystyle, 0, $align, $tfill);
                    }
                }
            }
            $fill = !$fill;
            $pdf1->Ln(); //Go to New Line
        }
        $pdf1->Cell($actualTableWidth, 0, '', 'T'); //Closing Line
        $pdf1->Ln();
        return $pdf1;
    }
    public static function writeBlock($pdf1, $blockOfText, $align = 'L', $settings = null)
    {
        $settings = __data__::combineArray($settings, $pdf1->getSettings());
        $settings = is_null($settings) ? array() : $settings;
        foreach ($blockOfText as $text) {
            self::writeLine($pdf1, $text, $align);
        }
        return $pdf1;
    }
    public static function writeLine($pdf1, $text, $align = 'L', $settings = null)
    {
        $settings = __data__::combineArray($settings, $pdf1->getSettings());
        $settings = is_null($settings) ? array() : $settings;
        /* align can be L, R or C */
        if ($text == (self::$__NEW_LINE)) {
            $pdf1->write(0, "\n", '', 0, 'C', true, 0, false, false, 0);
        } else {
            $pdf1->writeHTML($text, true, false, false, false, $align);
        }
        return $pdf1;
    }
    public static function createPageHeader($pdf1, $headerText, $logofile = null, $settings = null)
    {
        $settings = __data__::combineArray($settings, $pdf1->getSettings());
        $settings = is_null($settings) ? array() : $settings;
        if (!is_null($logofile)) $pdf1->setLogoFile($logofile);
        $pdf1->setHeaderText($headerText);
        return $pdf1;
    }
    public static function createPageFooter($pdf1, $footerText = null, $settings = null)
    {
        $settings = __data__::combineArray($settings, $pdf1->getSettings());
        $settings = is_null($settings) ? array() : $settings;
        if (!is_null($footerText)) $pdf1->setFooterText($footerText);
        return $pdf1;
    }
}
?>