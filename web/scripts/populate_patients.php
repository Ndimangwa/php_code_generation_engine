<?php 
require_once("../sys/__autoload__.php");
require_once("curl_exec.php");
$cookieFile = "cookies.txt";
$url = "http://localhost:8080/server/serviceAuthentication.php";
//$url = "http://localhost:8080/server/serviceTesting.php";
//Login 1st 
CurlEngine::execute($url, array(
    "username" => "admin",
    "password" => "admin"
),$cookieFile);
//Do Your Business
$url = "http://localhost:8080/server/servicePatientProcessor.php";
for ($i = 0; $i < 99; $i++)    {
    $numberInWords = Number::convertToWord($i+1);
    $numberInWords = str_replace(array('\'', '-'), " ", $numberInWords);
    $surname = ("Surname $numberInWords");
    $otherNames = "Patient $numberInWords";
    CurlEngine::execute($url, array(
        "__query__" => "create",
        "__classname__" => "Patient",
        "__log_message__" => "Created [ $surname ]",
        "registrationType" => (($i % 3) + 1),
        "status" => 1,
        "surname" => $surname,
        "otherNames" => $otherNames,
        "dob" => "05/06/1983",
        "sex" => (($i % 2) + 1),
        "address" => "Arusha"
    ), $cookieFile);
    echo "!";
}
?>