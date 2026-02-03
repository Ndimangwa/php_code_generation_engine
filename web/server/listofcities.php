<?php 
date_default_timezone_set("africa/dar_es_salaam");
$time = date("Y:m:d:H:i:s");
$larray1 = array(
    array("label" => "DSM", "value" => "Dar es Salaam"),
    array("label" => "MWZ", "value" => "Mwanza"),
    array("label" => "ARS", "value" => "Arusha"),
    array("label" => "MSH", "value" => "Moshi"),
    array("label" => "TIME", "value" => $time)
);
echo json_encode($larray1);
?>
