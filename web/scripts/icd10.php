<?php 
require_once("../sys/__autoload__.php");
$folderName = "../../templates/";
$sfilename = $folderName."icd10.csv";
$dfilename = $folderName."icd10_updated.csv";
$datafile1 = new DataFile($sfilename, null, null, function($dataLine1, $dataArray1, $options)    {
    DataFile::$a_count++;
    $translationArray1 = array(
        "M" => 1, "m" => 1,
        "F" => 2, "f" => 2,
        "Y" => 1, "y" => 1,
        "N" => 0, "n" => 0
    );
    $columnsToTranslate = array("Valid_ICD10_ClinicalUse", "Valid_ICD10_Primary", "Valid_ICD10_Asterisk", "Valid_ICD10_Dagger", "Valid_ICD10_Sequelae", "Gender");
    $dateColumns = array("WHO_Start_date", "WHO_End_date", "WHO_Revision_History", "SA_Start_Date", "SA_End_Date");
    $newDataArray1 = array();
    foreach ($dataArray1 as $colname => $valueBlock1)   {
        $newDataArray1[$colname] = array();
        foreach ($valueBlock1 as $value)    {
            //Removing comma
            $value = str_replace(",", "_@COMMA@_", $value);
            if ($colname == "id")   {
                $value = DataFile::$a_count;
            } else if ($value != "" && in_array($colname, $columnsToTranslate) && isset($translationArray1[$value]))    {
                $oldvalue = $value;
                $value = $translationArray1[$value];
                //echo "\n [colname, old-value, new-value] = [ $colname, $oldvalue, $value ]";
            } else if ($value != "" && in_array($colname, $dateColumns) && strlen($value) == 8)    {
                $value = substr($value, 0, 4).":".substr($value, 4, 2).":".substr($value, 6, 2).":00:00:00";
            } 
            $newDataArray1[$colname][sizeof($newDataArray1[$colname])] = $value;
        }
    }
    return $newDataArray1;
});

$datafile1->setFilename($dfilename)->write();
?>