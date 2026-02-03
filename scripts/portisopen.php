<?php 
require_once("../init/__object__.php");
require_once("../init/__network__.php");
if (sizeof($argv) < 4) {
    echo "\n============================================================================";
    echo "\n** The program will scan ports from a given address, then advance --      **";
    echo "\n**  - address until found open ports or the network is exhausted          **";
    echo "\n**                                                                        **";
    echo "\n** php portisopen.php ipAddress subnetMask port [port [port ...]]         **";
    echo "\n** Example: php portisopen.php 41.59.7.192 255.255.255.0 80 22 443 8080   **";
    die("\n============================================================================\n");
}
try {
    $network1 = new Network($argv[1], $argv[2]);
    $listofports = array();
    for ($i = 3; $i < sizeof($argv); $i++)  {
        $listofports[sizeof($listofports)] = intval($argv[$i]);
    }
    $numberofhost = $network1->getNumberOfHost();
    $currenthostnumber = $network1->getCurrentHostNumber();
    $error_code = null;
    $error_message = null;
    $timeout = 10;
    echo "\nInitial Host Number: $currenthostnumber ; Number of Host: $numberofhost \n";
    for ($i = $currenthostnumber; $i <= $numberofhost; $i++)    {
        $host = $network1->getIpAddress();
        $portsOpen = true;
        foreach ($listofports as $port)    {
            echo "\r".$host.":".$port;
            $connection = @fsockopen($host, $port, $error_code, $error_message, $timeout);
            if (! is_resource($connection))   {
                $portsOpen = false;
                break;
            }
        }
        if ($portsOpen) {
            echo "\rHost $host is available for specified ports\n";
            break;
        }
        if ($i != $numberofhost)    {
            $network1->advanceIpAddress();
        }
    }

} catch (Exception $e)  {
    echo "\nError Occured\n";
    die($e->getMessage());
}
?>