<?php 
class UITabularView extends UIView {
    public function __construct()   {
        parent::__construct();
    }
    public static function createView(
        $listOfObjectColumnsToDisplay,
        $listOfObjects, 
        $actionButtons = array(
            array("idColumn" => null,"nameColumn" => null,"caption" => "View","href" => "localhost/","appendId" => false, "link-classes" => null, "link-icons" => null)
        ),
        $dataTranslationArray = null /* arr[pname]['caption'] = "caption" , 
                                                ['values'][dbval] = displayVal
                                                ['urlArgsAppend'] = true 
                                                ['map'] = "Login.Sex.SexName"*/ ,
        $doNotDisplayColumns = null,
        $dataMinLength = 3,
        $maximumNumberOfDisplayedRowsPerPage = 64,
        $maximumNumberOfReturnedSearchRecords = 512,
        $formatDataForDisplay = null,
        $containerId = "__ui_tabular_view__ctn__001__"
    )  {
        if (is_null($actionButtons)) $actionButtons = array(
            array("idColumn" => null,"nameColumn" => null, "href" => "localhost/","appendId" => false)
        );
        if (is_null($dataTranslationArray)) $dataTranslationArray = array();
        
        $window1 = "<div id=\"$containerId\" class=\"ui-view ui-tabular-view\"><div class=\"bg-primary p-1\"><div class=\"bg-warning p-1\"><div class=\"bg-primary p-1\"><div class=\"bg-white p-1\">";
        /*Begin Your Content Here ---- 0001*/
        //A--Search Control 
        $window1 .= "<div><form><div class=\"input-group mb-3\"><input type=\"search\" class=\"ui-tabular-view-search form-control\" data-min-length=\"$dataMinLength\" placeholder=\"Search\"/><div class=\"input-group-append\"><button type=\"button\" class=\"btn btn-primary ui-tabular-view-btn-search\" data-toggle=\"tooltip\" title=\"Click to Search\">Search</button></div></div></form></div>";
        //B--Tabular
        $window1 .= "<div data-max-rows-per-page=\"$maximumNumberOfDisplayedRowsPerPage\" class=\"table-responsive\"><table class=\"table ui-tabular-view-table\">";
        $count = 0;
        foreach ($listOfObjects as $object1)  {
            if ($count == 0)    {
                //Develop thead 
                $window1 .= "<thead><tr><th scope=\"col\"></th>";
                foreach ($listOfObjectColumnsToDisplay as $pname)   {
                    if (! is_null($doNotDisplayColumns) && in_array($pname, $doNotDisplayColumns)) continue;
                    $colcaption = __object__::property2Caption($pname);
                    if (isset($dataTranslationArray[$pname]) && isset($dataTranslationArray[$pname]['caption'])) $colcaption = $dataTranslationArray[$pname]['caption'];
                    $window1 .= "<th>$colcaption</th>";
                }
                $window1 .= "<th></th></tr></thead><tbody>";
            }
            //Dealing with Data
            $sn = $count + 1;
            $window1 .= "<tr><th class=\"data-serial\" scope=\"row\">$sn</th>";
            $urlArgsAppend = null;
            foreach ($listOfObjectColumnsToDisplay as $pname)   {
                $value = $object1->getMyPropertyValue($pname);
                //We need to work on map
                if (is_null($value)) $value = "";
                if (isset($dataTranslationArray[$pname]) && isset($dataTranslationArray[$pname]['urlArgsAppend']))  {
                    if (is_null($urlArgsAppend)) $urlArgsAppend = "";
                    $urlArgsAppend .= "&$pname=$value";
                }
                if (! is_null($doNotDisplayColumns) && in_array($pname, $doNotDisplayColumns)) continue;
                if (isset($dataTranslationArray[$pname]) && isset($dataTranslationArray[$pname]['values']) && isset($dataTranslationArray[$pname]['values'][$value])) $value = $dataTranslationArray[$pname]['values'][$value];
                if (is_callable($formatDataForDisplay)) $value = $formatDataForDisplay($object1->__get_connection__(), $pname, $value);
                $window1 .= "<td class=\"data-search\">$value</td>";
            }
            //Action Buttons
            $window1 .= "<td><span style=\"white-space: nowrap;\">";
            $rowId = null;
            $rowName = null;
            $actionCount = 0;
            foreach ($actionButtons as $actionButton)   {
                //actionButtons
                $actionButtonIdColumn = null; if (isset($actionButton["idColumn"])) $actionButtonIdColumn = $actionButton["idColumn"];
                if (! is_null($actionButtonIdColumn) && isset($record1[$actionButtonIdColumn])) $rowId = $record1[$actionButtonIdColumn];
                $actionButtonNameColumn = null; if (isset($actionButton["nameColumn"])) $actionButtonNameColumn = $actionButton["nameColumn"];
                if (! is_null($actionButtonNameColumn) && isset($record1[$actionButtonNameColumn])) $rowName = $record1[$actionButtonNameColumn];
                $actionButtonCaption = null; if (isset($actionButton["caption"])) $actionButtonCaption = $actionButton["caption"];
                $actionButtonForwardURL = null; if (isset($actionButton["href"])) $actionButtonForwardURL = $actionButton["href"];
                $blnAppendId = false; if (isset($actionButton["appendId"])) $blnAppendId = $actionButton["appendId"];
                $actionButtonTitle = null; if (isset($actionButton["title"])) $actionButtonTitle = $actionButton["title"];
                $actionButtonLinkClasses = ""; if (isset($actionButton["link-classes"])) $actionButtonLinkClasses = $actionButton["link-classes"];
                $actionButtonLinkIcons = null; if (isset($actionButton["link-icons"])) $actionButtonLinkIcons = $actionButton["link-icons"];
                if ($blnAppendId && is_null($rowId)) throw new Exception("Append Id while Column Id not present");
                $linkhref = $actionButtonForwardURL;
                if ($blnAppendId && ! is_null($rowId)) $linkhref .= $rowId;
                if (! is_null($urlArgsAppend)) $linkhref .= $urlArgsAppend;
                $title = null; 
                if (! is_null($actionButtonTitle)) $title = $actionButtonTitle;
                if (! is_null($rowName)) { if (is_null($title)) $title = $rowName; else $title = str_replace("##REPLACE##", $rowName, $title); }
                if (! is_null($title)) $title = " data-toggle=\"tooltip\" title=\"$title\"";
                else $title = "";
                if ($actionCount > 0) {
                    $actionButtonLinkClasses .= " ml-2";
                }
                $actionButtonLinkClasses = "class=\"$actionButtonLinkClasses\"";
                $window1 .= "<a href=\"$linkhref\" $actionButtonLinkClasses $title>";
                if (! is_null($actionButtonLinkIcons)) $window1 .= "<i class=\"$actionButtonLinkIcons\"></i>";
                if (! is_null($actionButtonCaption)) $window1 .= $actionButtonCaption;
                $window1 .= "</a>";
                $actionCount++;
            }
            $window1 .= "</span></td></tr>";
            $count++;
        }
        $window1 .= "</tbody></table></div>";
        
        /*End your Content Here ---- 0001*/
        $window1 .= "</div></div></div></div></div>";
        return $window1;
    }
    public static function query(
        $conn,
        $sqlquery /*SELECT * FROM _tablename*/, 
        $actionButtons = array(
            array("idColumn" => null,"nameColumn" => null,"caption" => "View","href" => "localhost/","appendId" => false, "link-classes" => null, "link-icons" => null)
        ),
        $dataTranslationArray = null /* arr[dbcolname]['caption'] = "caption" , 
                                                      ['values'][dbval] = displayVal
                                                      ['urlArgsAppend'] = true */ ,
        $doNotDisplayColumns = null,
        $dataMinLength = 3,
        $maximumNumberOfDisplayedRowsPerPage = 64,
        $maximumNumberOfReturnedSearchRecords = 512,
        $formatDataForDisplay = null,
        $containerId = "__ui_tabular_view__ctn__001__"
    )  {
        if (is_null($actionButtons)) $actionButtons = array(
            array("idColumn" => null,"nameColumn" => null, "href" => "localhost/","appendId" => false)
        );
        if (is_null($dataTranslationArray)) $dataTranslationArray = array();
        
        $records = __data__::getSelectedRecords($conn, $sqlquery, false);
        $records = $records['column'];
        $window1 = "<div id=\"$containerId\" class=\"ui-view ui-tabular-view\"><div class=\"bg-primary p-1\"><div class=\"bg-warning p-1\"><div class=\"bg-primary p-1\"><div class=\"bg-white p-1\">";
        /*Begin Your Content Here ---- 0001*/
        //A--Search Control 
        $window1 .= "<div><form><div class=\"input-group mb-3\"><input type=\"search\" class=\"ui-tabular-view-search form-control\" data-min-length=\"$dataMinLength\" placeholder=\"Search\"/><div class=\"input-group-append\"><button type=\"button\" class=\"btn btn-primary ui-tabular-view-btn-search\" data-toggle=\"tooltip\" title=\"Click to Search\">Search</button></div></div></form></div>";
        //B--Tabular
        $window1 .= "<div data-max-rows-per-page=\"$maximumNumberOfDisplayedRowsPerPage\" class=\"table-responsive\"><table class=\"table ui-tabular-view-table\">";
        $count = 0;
        foreach ($records as $record1)  {
            if ($count == 0)    {
                //Develop thead 
                $window1 .= "<thead><tr><th scope=\"col\"></th>";
                foreach ($record1 as $colname => $colval)   {
                    if (! is_null($doNotDisplayColumns) && in_array($colname, $doNotDisplayColumns)) continue;
                    $colcaption = __object__::property2Caption($colname);
                    if (isset($dataTranslationArray[$colname]) && isset($dataTranslationArray[$colname]['caption'])) $colcaption = $dataTranslationArray[$colname]['caption'];
                    $window1 .= "<th>$colcaption</th>";
                }
                $window1 .= "<th></th></tr></thead><tbody>";
            }
            //Dealing with Data
            $sn = $count + 1;
            $window1 .= "<tr><th class=\"data-serial\" scope=\"row\">$sn</th>";
            $urlArgsAppend = null;
            foreach ($record1 as $colname => $colval)   {
                if (isset($dataTranslationArray[$colname]) && isset($dataTranslationArray[$colname]['urlArgsAppend']))  {
                    if (is_null($urlArgsAppend)) $urlArgsAppend = "";
                    $urlArgsAppend .= "&$colname=$colval";
                }
                if (! is_null($doNotDisplayColumns) && in_array($colname, $doNotDisplayColumns)) continue;
                if (isset($dataTranslationArray[$colname]) && isset($dataTranslationArray[$colname]['values']) && isset($dataTranslationArray[$colname]['values'][$colval])) $colval = $dataTranslationArray[$colname]['values'][$colval];
                if (is_callable($formatDataForDisplay)) $colval = $formatDataForDisplay($conn, $colname, $colval);
                $window1 .= "<td class=\"data-search\">$colval</td>";
            }
            //Action Buttons
            $window1 .= "<td><span style=\"white-space: nowrap;\">";
            $rowId = null;
            $rowName = null;
            $actionCount = 0;
            foreach ($actionButtons as $actionButton)   {
                //actionButtons
                $actionButtonIdColumn = null; if (isset($actionButton["idColumn"])) $actionButtonIdColumn = $actionButton["idColumn"];
                if (! is_null($actionButtonIdColumn) && isset($record1[$actionButtonIdColumn])) $rowId = $record1[$actionButtonIdColumn];
                $actionButtonNameColumn = null; if (isset($actionButton["nameColumn"])) $actionButtonNameColumn = $actionButton["nameColumn"];
                if (! is_null($actionButtonNameColumn) && isset($record1[$actionButtonNameColumn])) $rowName = $record1[$actionButtonNameColumn];
                $actionButtonCaption = null; if (isset($actionButton["caption"])) $actionButtonCaption = $actionButton["caption"];
                $actionButtonForwardURL = null; if (isset($actionButton["href"])) $actionButtonForwardURL = $actionButton["href"];
                $blnAppendId = false; if (isset($actionButton["appendId"])) $blnAppendId = $actionButton["appendId"];
                $actionButtonTitle = null; if (isset($actionButton["title"])) $actionButtonTitle = $actionButton["title"];
                $actionButtonLinkClasses = ""; if (isset($actionButton["link-classes"])) $actionButtonLinkClasses = $actionButton["link-classes"];
                $actionButtonLinkIcons = null; if (isset($actionButton["link-icons"])) $actionButtonLinkIcons = $actionButton["link-icons"];
                if ($blnAppendId && is_null($rowId)) throw new Exception("Append Id while Column Id not present");
                $linkhref = $actionButtonForwardURL;
                if ($blnAppendId && ! is_null($rowId)) $linkhref .= $rowId;
                if (! is_null($urlArgsAppend)) $linkhref .= $urlArgsAppend;
                $title = null; 
                if (! is_null($actionButtonTitle)) $title = $actionButtonTitle;
                if (! is_null($rowName)) { if (is_null($title)) $title = $rowName; else $title = str_replace("##REPLACE##", $rowName, $title); }
                if (! is_null($title)) $title = " data-toggle=\"tooltip\" title=\"$title\"";
                else $title = "";
                if ($actionCount > 0) {
                    $actionButtonLinkClasses .= " ml-2";
                }
                $actionButtonLinkClasses = "class=\"$actionButtonLinkClasses\"";
                $window1 .= "<a href=\"$linkhref\" $actionButtonLinkClasses $title>";
                if (! is_null($actionButtonLinkIcons)) $window1 .= "<i class=\"$actionButtonLinkIcons\"></i>";
                if (! is_null($actionButtonCaption)) $window1 .= $actionButtonCaption;
                $window1 .= "</a>";
                $actionCount++;
            }
            $window1 .= "</span></td></tr>";
            $count++;
        }
        $window1 .= "</tbody></table></div>";
        
        /*End your Content Here ---- 0001*/
        $window1 .= "</div></div></div></div></div>";
        return $window1;
    }
    public static function buildHTMLTableForObject($object1, $listOfProperties, $headerArray1 = array('Caption', 'Values'), $captionArray1 = null, $mapArray1 = null, $emptyValueArray = null, $widthRatioArray1 = array(1, 4, 4), $styleArray1 = array('ele1' => array('width' => '5px')), $settings = array(
        'enable-serial-counting' => true,
        'serial-counting-begin-at' => 1,
        'serial-counting-step' => 1,
        'serial-counting-title' => 'S/N'
    ), $containerId = null, $profile1 = null )    {
        $__DEFAULT_RETURN_VALUE = "";
       $headerArray1 = (! is_null($headerArray1) && (sizeof($headerArray1) == 2)) ? $headerArray1 : array('Caption', 'Value');
       $widthRatioArray1 = (! is_null($widthRatioArray1)) ? $widthRatioArray1 : array(1, 4, 4);
       $captionArray1 = (! is_null($captionArray1)) ? $captionArray1 : array(); 
       //Now working with table
       $tabularData = __data__::getObjectData($object1, $listOfProperties, $mapArray1, $emptyValueArray);  
       if (is_null($tabularData)) return $__DEFAULT_RETURN_VALUE;
       $dataArray1 = array();
       foreach ($tabularData as $pname => $datablock1)  {
           foreach ($datablock1 as $value)  {
               //Getting caption
               $caption = isset($captionArray1[$pname]) ? $captionArray1[$pname] : ( __object__::property2Caption($pname) );
               $dataArray1[sizeof($dataArray1)] = array($caption, $value);
           }
       }
       if (sizeof($dataArray1) == 0) return $__DEFAULT_RETURN_VALUE;
       return ( self::buildHTMLTable($headerArray1, $dataArray1, $widthRatioArray1, $containerId, $styleArray1, $settings, $profile1) );
    }
    public static function buildHTMLTable($headerArray1, $dataArray1, $widthRatioArray1 = null, $containerId = null, $styleArray1 = array('ele1' => array('width' => '5px')), $settings = array(
        'enable-serial-counting' => true,
        'serial-counting-begin-at' => 1,
        'serial-counting-step' => 1,
        'serial-counting-title' => 'S/N'
    ), $profile1 = null) {
        $settings = is_null($settings) ? array() : $settings;
        $containerId = is_null($containerId) ? ( __object__::getMD5CodedString("Tabular", 32) ) : $containerId;
        //profile is to apply page navigation -- later 
        $window1 = "<div id=\"$containerId\">";
        //Style Information
        if (! is_null($styleArray1))    {
            $style = "";
            foreach ($styleArray1 as $ele1 => $eleCSSBlock1)    {
                $tsyle = "$ele1 {";
                foreach ($eleCSSBlock1 as $key => $value)   {
                    $dt = " $key : $value;";
                    $tsyle .= $dt;
                }
                $tsyle .= "}";
                //At the end append 
                $style = ($style == "") ? $tsyle : ( $style . " " . $tsyle );
            }
            $window1 .= "<style type=\"text/css\">$style</style>";
        }
        //Now working with table
        //serial-counting
        $enableSerialCounting = isset($settings['enable-serial-counting']) ? $settings['enable-serial-counting'] : false;
        $currentSerialCounting = isset($settings['serial-counting-begin-at']) ? $settings['serial-counting-begin-at'] : 1;
        $serialCountingStep = isset($settings['serial-counting-step']) ? $settings['serial-counting-step'] : 1;
        $serialCountingTitle = isset($settings['serial-counting-title']) ? $settings['serial-counting-title'] : 'S/N';      
        $columncount = sizeof($headerArray1) + ( $enableSerialCounting ? 1 : 0 );
        if (is_null($widthRatioArray1)) {
            $widthRatioArray1 = array();
            for ($i =0; $i < $columncount; $i++)    $widthRatioArray1[$i] = 1;
        }
        if (sizeof($widthRatioArray1) < $columncount)  {
            //Prepend
            $tempArray1 = array();
            $len = sizeof($widthRatioArray1);
            $diff = $columncount - $len;
            for ($i = 0; $i < $columncount; $i++)   {
                $tempArray1[$i] = ($i < $diff) ? 1 : $widthRatioArray1[$i - $diff];
            }
            $widthRatioArray1 = $tempArray1;
        }
        //Now we need to calculate percentage width 
        $sumWidth = 0;
        foreach ($widthRatioArray1 as $index => $width)    {
            $sumWidth += $width;
        }
        if ($sumWidth > 0)  {
            foreach ($widthRatioArray1 as $index => $width)    {
                $widthRatioArray1[$index] = ( $width * 100 / $sumWidth );
            }
        }
        $window1 .= "<table class=\"table\"><thead class=\"thead\"><tr class=\"thead-tr-0\">";
        //Working with header 
        $t1 = ""; $hcount = 0;
        if ($enableSerialCounting) {
            $width = $widthRatioArray1[$hcount];
            $hcount++;
            $t1 = "<th scope=\"col\" style=\"width: $width%;\">$serialCountingTitle</th>";
        }
        foreach ($headerArray1 as $header)  {
            $width = $widthRatioArray1[$hcount];
            $hcount++;
            $t1 .= "<th scope=\"col\" style=\"width: $width%;\">$header</th>";
        }
        $window1 .= "$t1</tr></thead><tbody class=\"tbody\">";
        foreach ($dataArray1 as $index => $rowDataArray1) {
            $rowclass = "tbody-tr-".$index;
            $window1 .= "<tr class=\"$rowclass\">";
            $noofcols = $columncount;
            if ($enableSerialCounting) {
                $hcount = ($columncount - $noofcols);
                $cellclass = "cell-".$hcount;
                $width = $widthRatioArray1[$hcount];
                $window1 .= "<th scope=\"row\" class=\"row-sn $cellclass\" style=\"width: $width%;\">$currentSerialCounting</th>";
                $currentSerialCounting += $serialCountingStep;
                $noofcols--;
            }
            foreach ($rowDataArray1 as $cellvalue)   {
                if ($noofcols > 0)  {
                    $hcount = ($columncount - $noofcols);
                    $cellclass = "cell-".$hcount;
                    $width = $widthRatioArray1[$hcount];
                    $window1 .= "<td class=\"data-cell $cellclass\" style=\"width: $width%;\">$cellvalue</td>";
                    $noofcols--;
                }
            }
            //Now fill the remaining cells if remaining 
            for ($i = 0; $i < $noofcols; $i++)  {
                $hcount = ($columncount - $noofcols);;
                $cellclass = "cell-".$hcount;
                $width = $widthRatioArray1[$hcount];
                $window1 .= "<td class=\"data-cell $cellclass\" style=\"width: $width%;\"></td>";
            }
            $window1 .= "</tr>";
        }
        $window1 .= "</tbody></table>";
        $window1 .= "</div>";
        return $window1;
    }
}
?>