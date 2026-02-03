<?php 
class ContextManager extends __object__	{
    public final static function isSystemDefaultAllowed($database, $conn)   {
        $query = "{ \"query\": \"select\", \"tables\": [ { \"table\" : \"_contextManager\" } ], \"cols\" : [ { \"col\" : \"defaultXValue\" } ] }";
        $jresult1 = SQLEngine::execute($query, $conn);
        if (is_null($jresult1)) throw new Exception("Malformed Query");
        $jArray1 = json_decode($jresult1, true);
        if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
		if ($jArray1['count'] !== 1) throw new Exception("Duplicate or no record found");
        $resultSet = $jArray1['rows'][0];
        if (! array_key_exists("defaultXValue", $resultSet)) throw new Exception("Column [defaultXValue] not available while pulling data");
		return (intval($resultSet['defaultXValue']) == 1);
    }
    public final static function setSystemDefaultAllowed($database, $conn, $sysAllowedValue)    {
        $query = "{ \"query\" : \"update\", \"table\" : \"_contextManager\", \"cols\" : [ { \"defaultXValue\" : \"$sysAllowedValue\" } ] }";
        $jresult1 = SQLEngine::execute($query, $conn);
        if (is_null($jresult1)) throw new Exception("Malformed Query");
        $jArray1 = json_decode($jresult1, true);
        if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
    }
}
?>