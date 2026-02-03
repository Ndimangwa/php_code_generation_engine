<?php
require_once("../html/vendor/autoload.php");
require_once("../html/sys/__autoload__.php");
$date1 = new DateAndTime("1983:5:6:23:0:1");
$tstring1 = $date1->getDateAndTimeString();
$tstamp1 = $date1->getTimestamp();
echo "\nOriginal Time is [ $tstring1 , $tstamp1 ]\n";
$tomorrow1 = $date1->getNextDateAndTimeByDays(1);
$yesterday1 = $date1->getPreviousDateAndTimeByDays(9);
$tomorrowString1 = $tomorrow1->getDateAndTimeString();
$tomorrowStamp1 = $tomorrow1->getTimestamp();
$yesterdayString1 = $yesterday1->getDateAndTimeString();
$yesterdayStamp1 = $yesterday1->getTimestamp();
echo "\nYesterday(9) Time was [ $yesterdayString1 , $yesterdayStamp1 ]\n";
echo "\nTomorrow(1) Time is [ $tomorrowString1 , $tomorrowStamp1 ]\n";
echo "\nWorking with Timestamp\n";
$obj1 = new DateAndTime($yesterdayString1);
$dtString1 = $obj1->getDateAndTimeString();
$dtStamp1 = $obj1->getTimestamp();
echo "\nRecreated from Object [ $dtString1 , $dtStamp1 ]\n";
?>

