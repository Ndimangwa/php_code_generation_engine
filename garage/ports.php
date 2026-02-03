<?php 
$host = 'stackoverflow.com';
$ports = array(21, 25, 80, 81, 110, 443, 3306);
$error_code = null;
$error_message = null;
$timeout = 10;

foreach ($ports as $port)
{
    $connection = fsockopen($host, $port, $error_code, $error_message, $timeout);

    if (is_resource($connection))
    {
        echo '<h2>' . $host . ':' . $port . ' ' . '(' . getservbyport($port, 'tcp') . ') is open.</h2>' . "\n";

        fclose($connection);
    }

    else
    {
        echo '<h2>' . $host . ':' . $port . ' is not responding.</h2>' . "\n";
    }
}
?>
