<?php 
$host = "127.0.0.1";
$dbname1 = "dbtest";
$dbname2 = "dbhmis";
$username = "ndimangwa";
$password = "mysql";
$contextArray1 = array(
    array(
        "tablename" => "_login",
        "id" => "loginId",
        "context" => "context"
    ), 
    array(
        "tablename" => "_jobTitle",
        "id" => "jobId",
        "context" => "context"
    ),
    array(
        "tablename" => "_groups",
        "id" => "groupId",
        "context" => "context"
    )
);
try {
    $conn1 = new PDO("mysql:host=$host;dbname=$dbname1", $username, $password);
    $conn2 = new PDO("mysql:host=$host;dbname=$dbname2", $username, $password);

    foreach ($contextArray1 as $contextBlock1)  {
        $tablename = $contextBlock1['tablename'];
        $idcolumn = $contextBlock1['id'];
        $contextcolumn = $contextBlock1['context'];

        $passwordColumn = null;
        if ($tablename == "_login") $passwordColumn = "password";

        //Read from $conn1
        $query = "SELECT $idcolumn, $contextcolumn FROM $tablename";
        if (! is_null($passwordColumn)) {
            $query = "SELECT $idcolumn, $contextcolumn, $passwordColumn FROM $tablename";
        }
        $stmnt = $conn1->query($query);
        foreach ($stmnt->fetchAll(PDO::FETCH_ASSOC) as $record) {
            $id = $record[$idcolumn];
            $context = $record[$contextcolumn];
            //Now we can update
            $query = "UPDATE $tablename SET $contextcolumn='$context' WHERE $idcolumn='$id'";
            if (! is_null($passwordColumn)) {
                $password = $record[$passwordColumn];
                $query = "UPDATE $tablename SET $contextcolumn='$context', $passwordColumn='$password' WHERE $idcolumn='$id'";
            }
            $conn2->exec($query);
            echo "!";
        }
    }

    $conn2 = null;
    $conn1 = null;
} catch (Exception $e)  {
    die($e->getMessage());
}
echo "\nCompleted\n";
?>