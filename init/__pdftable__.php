<?php 
class PDFTable extends PDFComponent {
    private $pdfObject1 = null;
    private function __construct($pdfObject1) {
      $this->pdfObject1 = $pdfObject1;
    }
    public static function getTableInstance($pdfObject1)   {
        return ( new PDFTable($pdfObject1) );
    }
    public static function getTable($pdf1, $dataArray1, $headerArray1, $columnRatioArray1 = null, $dataFormatArray1 = null, $maximumWidth = 180, $settings = array(
        'enable-serial-counting' => true,
        'serial-counting-begin-at' => 1,
        'serial-counting-step' => 1,
        'serial-counting-title' => 'S/N',
        'table-title' => 'Default Title',
        'default-empty-cell-value' => ''
    ))   {
        //Make sure settings is an array
        $settings = is_null($settings) ? array() : $settings;
        $columnRatioArray1 = is_null($columnRatioArray1) ? array() : $columnRatioArray1;
        $dataFormatArray1 = is_null($dataFormatArray1) ? array() : $dataFormatArray1;
        //serial-counting
        $enableSerialCounting = __object__::getValueFromArray($settings, 'enable-serial-counting', false);
        $currentSerialCounting = __object__::getValueFromArray($settings, 'serial-counting-begin-at', 1);
        $serialCountingStep = __object__::getValueFromArray($settings, 'serial-counting-step', 1);
        $serialCountingTitle = __object__::getValueFromArray($settings, 'serial-counting-title', 'S/N');
        //Column Size
        $columncount = sizeof($headerArray1) + ( $enableSerialCounting ? 1 : 0 );
        //Make sure columnRatioArray1 is okay 
        $len = sizeof($columnRatioArray1);
        if ($len != $columncount)    {
            $tempArray1 = array();
            if ($len < $columncount)    {
                //prepend 
                $diff = $columncount - $len;
                for ($i = 0; $i < $columncount; $i++) $tempArray1[$i] = ($i < $diff) ? 1 : $columnRatioArray1[$i - $diff];
            } else {
                for ($i = 0; $i < $columncount; $i++)   $tempArray1[$i] = $columnRatioArray1[$i];
            }
            $columnRatioArray1 = $tempArray1;
        }
        //We need to draw table title
        $tableTitle = __object__::getValueFromArray($settings, 'table-title', null);
        if (! is_null($tableTitle)) {
            $pdf1->Cell(0, 0, $tableTitle, 1, 1, 'C', 0, '', 0);
            $pdf1->Ln();
        }
        //Build table
        //Now we need to assemble data properly
        $tempDataArray1 = array();
        $defaultEmptyCellValue = __object__::getValueFromArray($settings, 'default-empty-cell-value', '');
        foreach ($dataArray1 as $index => $rowData1)    {
            //Make sure each $rowData1 has same 
            $len = sizeof($rowData1);
            if ($len != $columncount)   {
                if ($len < $columncount)    {
                   $sn = $index + 1;
                   $diff = $columncount - $len;
                   for ($i = 0; $i < $columncount; $i++)  $tempDataArray1[$index][$i] = ($i < $diff) ? ( ($i == 0) ? $sn : $defaultEmptyCellValue ) : $rowData1[$i - $diff];    
                } else {
                    //len > columncount
                    for ($i = 0; $i < $columncount; $i++) $tempDataArray1[$index][$i] = $rowData1[$i];
                }
            } else {
                $tempDataArray1[$index] = $rowData1;
            }
            //Now adjusting correcting
            $tempArray1 = array(
                'data' => $tempDataArray1[$index],
                'format' => $dataFormatArray1
            );
            $tempDataArray1[$index] = $tempArray1;
        }
        $dataArray1 = $tempDataArray1;
        return ( PDFLayout::writeTable($pdf1, $dataArray1, $headerArray1, $columnRatioArray1, $maximumWidth, $settings) );
    }
}
?>