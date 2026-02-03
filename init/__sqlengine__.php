<?php 
//This class relies heavily on JSON2SQL class
class SQLEngine {
    public static $code_success = 0;
    public static $code_failed = 1023;
    public static $message_ok = "OK";
    private static function __execute_gen_update_($queryArray1, $conn, $qtype, $rollback = true)    {
        $pdoObj1 = array();
        $pdoObj1['query'] = $qtype;
        try {
            $stmt = $conn->prepare($queryArray1['pdo-query']);
            if (! $stmt) throw new PDOException("Malformed query");
            if ($rollback) $conn->beginTransaction();
            if (! $stmt->execute($queryArray1['query-values'])) throw new PDOException("Failed to execute query ".json_encode($stmt->errorInfo()));
            $pdoObj1['code'] = self::$code_success;
            $pdoObj1['message'] = self::$message_ok;
            if ($qtype == "insert") $pdoObj1['id'] = $conn->lastInsertId();
            if ($rollback) $conn->commit();
        } catch (PDOException $e)   {
            if ($rollback) $conn->rollBack();
            $pdoObj1['code'] = self::$code_failed;
            $pdoObj1['message'] = $e->getMessage();
            
        }
        return $pdoObj1;
    }
    private static function executeInsert($queryArray1, $conn, $rollback = true)  {
        return self::__execute_gen_update_($queryArray1, $conn, "insert", $rollback);
    }
    private static function executeDelete($queryArray1, $conn, $rollback = true)  {
        return self::__execute_gen_update_($queryArray1, $conn, "delete", $rollback);
    }
    private static function executeUpdate($queryArray1, $conn, $rollback = true)  {
        return self::__execute_gen_update_($queryArray1, $conn, "update", $rollback);
    }
    private static function executeSelect($queryArray1, $conn)  {
        $pdoObj1 = array();
        $pdoObj1['query'] = "select";
        try {
            $stmt = $conn->prepare($queryArray1['pdo-query']);
            if (! $stmt) throw new PDOException("Malformed query");
            if (! $stmt->execute($queryArray1['query-values'])) throw new PDOException("Failed to execute query");
            //----
            $pdoObj1['count'] = $stmt->rowCount();
            $pdoObj1['code'] = self::$code_success;
            $pdoObj1['message'] = self::$message_ok;
            $pdoObj1['rows'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e)   {
            $pdoObj1['code'] = self::$code_failed;
            $pdoObj1['message'] = $e->getMessage();
        }
        return $pdoObj1;
    }
    public final static function rawSelectQueryExecute($query, $conn)   {
        $queryArray1 = array();
        $queryArray1['pdo-query'] = $query;
        $queryArray1['old-query'] = $query;
        $queryArray1['query-values'] = array();
        $jresults = self::executeSelect($queryArray1, $conn);
        if (! is_null($jresults)) $jresults = json_encode($jresults);
        return $jresults;
    }
    public final static function execute($jstring, $conn, $rollback = true) {
        //Whoever want to make connection must supply the connection 
        if (is_null($jstring)) return null;
        $jArray1 = json_decode($jstring, true);
        if (is_null($jArray1) || ! isset($jArray1['query']) ) return null;
        $queryArray1 = JSON2SQL::buildQuery($jArray1);
        if (is_null($queryArray1)) return null;
        $jresults = null;
        switch ($jArray1['query'])  {
            case "select":
                $jresults = self::executeSelect($queryArray1, $conn);
                break;
            case "insert":
                $jresults = self::executeInsert($queryArray1, $conn, $rollback);
                break;
            case "update":
                $jresults = self::executeUpdate($queryArray1, $conn, $rollback);
                break;
            case "delete":
                $jresults = self::executeDelete($queryArray1, $conn, $rollback);
                break;
            default:;
        }
        if (! is_null($jresults)) $jresults = json_encode($jresults);
        return $jresults;
    }
    public final static function executeWithArgs($jstring, $argArray1)  {
        if (is_null($argArray1) || ! is_array($argArray1)) throw new PDOException("Argument Array not set or not an array");
        if (! isset($argArray1['host'])) throw new PDOException("Host is not set");
        if (! isset($argArray1['dbname'])) throw new PDOException("Dbname is not set");
        if (! isset($argArray1['username'])) throw new PDOException("Username is not set");
        if (! isset($argArray1['password'])) throw new PDOException("Password is not set");
        $host = $argArray1['host'];
        $dbname = $argArray1['dbname'];
        $username = $argArray1['username'];
        $password = $argArray1['password'];
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $jresults = self::execute($jstring, $conn);
        $conn = null;
        return $jresults;
    }
}
?>
