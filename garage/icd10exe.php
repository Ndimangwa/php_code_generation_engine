<?php
require_once("../html/vendor/autoload.php");
require_once("../html/sys/__autoload__.php");
$colWHOStartDate = "WHO_Start_date";
$colWHOEndDate = "WHO_End_date";
$colWHORevisionHistory = "WHO_Revision_History";
$colSAStartDate = "SA_Start_Date";
$colSAEndDate = "SA_End_Date";
$datafile1 = new DataFile("../templates/icd10_updated.csv");
foreach ($datafile1->getDataLines() as $dataLine1) {
    $whoStartDate = $dataLine1->getValuesAtColumns($colWHOStartDate, 0);
    $whoEndDate = $dataLine1->getValuesAtColumns($colWHOEndDate, 0);
    $whoRevisionHistory = $dataLine1->getValuesAtColumns($colWHORevisionHistory, 0);
    $saStartDate = $dataLine1->getValuesAtColumns($colSAStartDate, 0);
    $saEndDate = $dataLine1->getValuesAtColumns($colSAEndDate, 0);
    //No Converting
    //01 --
    try {
        $dt1 = new DateAndTime($whoStartDate);
        $dataLine1->setValuesAtColumns($colWHOStartDate, $dt1->getTimestamp(), 0);
    } catch (Exception $e) {
    }
    //02 --
    try {
        $dt1 = new DateAndTime($whoEndDate);
        $dataLine1->setValuesAtColumns($colWHOEndDate, $dt1->getTimestamp(), 0);
    } catch (Exception $e)  {
    }
    //03 --
    try {
        $dt1 = new DateAndTime($whoRevisionHistory);
        $dataLine1->setValuesAtColumns($colWHORevisionHistory, $dt1->getTimestamp(), 0);
    } catch (Exception $e)  {
    }
    //04 --
    try {
        $dt1 = new DateAndTime($saStartDate);
        $dataLine1->setValuesAtColumns($colSAStartDate, $dt1->getTimestamp(), 0);
    } catch (Exception $e)  {}
    //05 --
    try {
        $dt1 = new DateAndTime($saEndDate);
        $dataLine1->setValuesAtColumns($colSAEndDate, $dt1->getTimestamp(), 0);
    } catch (Exception $e)  {}
}
$datafile1->setFilename("lattest_updated_icd10.csv");
$datafile1->write();
?>