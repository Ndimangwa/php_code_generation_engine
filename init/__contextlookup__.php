<?php
/******************************************************
**                                                   **
**             CLASSNAME : ContextLookup             **
**  Copyright (c) Zoomtong Company Limited           **
**  Developed by : Ndimangwa Fadhili Ngoya           **
**  Timestamp    : 2021:05:25:13:34:00               **
**  Phones       : +255 787 101 808 / 762 357 596    **
**  Email        : ndimangwa@gmail.com               **
**  Address      : P.O BOX 7436 MOSHI, TANZANIA      **
**                                                   **
**  Dedication to my dear wife Valentina             **
**                my daughters Raheli & Keziah       **
**                                                   **
*******************************************************/
class ContextLookup extends __data__ {
	protected $database;
	protected $conn;
	private $contextId;
	private $symbol;
	private $value;
	private $extraFilter;
	private $extraInformation;
	private $flags;
/*BEGIN OF CUSTOM CODES : You should Add Your Custom Codes Below this line*/
    public static $__DENY = 0;
	public static $__ALLOW = 1;
    public static $__DONOTCARE = 2;
    public static $__ALLOW_ALL = 1;
	public static $__DENY_ALL = 2;
	public static $__DONOTCARE_ALL = 3;
	public static $__CUSTOMIZE = 4;
    public final static function getSymbolFromValue($database, $conn, $value)   {
        //$query = "{ \"query\" : \"select\", \"tables\" : [ { \"table\" : \"_contextLookup\" } ], \"cols\" : [ { \"col\" : \"symbol\" } ], \"where\": { \"_value\" : \"'$value'\" }  }";
		//$jresult1 = SQLEngine::execute($query, $conn);
		$jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
			array('_contextLookup'),
			array('symbol'),
			array('_value' => $value)
		),$conn);
        if (is_null($jresult1)) throw new Exception("Malformed Query");
        $jArray1 = json_decode($jresult1, true);
        if ($jArray1['code'] != 0) throw new Exception($jArray1['message']);
		if ($jArray1['count'] != 1) throw new Exception("Duplicate or no record found");
        $resultSet = $jArray1['rows'][0];
        if (! array_key_exists("symbol", $resultSet)) throw new Exception("Column [symbol] not available while pulling data");
		return $resultSet['symbol'];
    }
    public final static function getValueFromSymbol($database, $conn, $symbol)  {
        //Can be a, A etc
        //$query = "{ \"query\" : \"select\", \"tables\" : [ { \"table\" : \"_contextLookup\" } ], \"cols\" : [ { \"col\" : \"symbol\" }, { \"col\" : \"_value\" } ], \"where\" : { \"symbol\" : \"'$symbol'\" } }";
		//$jresult1 = SQLEngine::execute($query, $conn);
		$jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
			array('_contextLookup'),
			array('symbol', '_value'),
			array('symbol' => $symbol)
		),$conn);
        if (is_null($jresult1)) throw new Exception("Malformed Query");
		$jArray1 = json_decode($jresult1, true);
        if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
		$valueToReturn = null; //We need now to proceed manually since both 'a' and 'A' will be returned
        foreach ($jArray1['rows'] as $dataArray1)   {
            if ($dataArray1['symbol'] == $symbol)   {
                $valueToReturn = $dataArray1['_value'];
                break;
            }
		}
		if (is_null($valueToReturn)) throw new Exception("ContextLookup, Exactly symbol was not found");
        return $valueToReturn;
    }   
/*END OF CUSTOM CODES : You should Add Your Custom Codes Above this line*/
	public static function create($database, $id, $conn) { return new ContextLookup($database, $id, $conn); }
	public function __construct($database, $id, $conn)    {
		$this->database = $database;
		$this->conn = $conn;
		$tablename = self::getTablename();
		//$whereClause = self::getId0Columnname();
		$id0Columnname = self::getId0Columnname();
		//$whereClause = "{ \"".$whereClause."\" : $id }";
		//$query = "{\"query\" : \"select\", \"tables\": [ { \"table\" : \"".$tablename."\" } ], \"cols\" : [ { \"col\" : \"*\" } ], \"where\" : $whereClause }";
		$jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
			array($tablename),
			array('*'),
			array($id0Columnname => $id)
		), $conn);
		$jArray1 = json_decode($jresult1, true);
		if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
		if ($jArray1['count'] !== 1) throw new Exception("Duplicate or no record found");
		$resultSet = $jArray1['rows'][0];
		if (! array_key_exists("contextId", $resultSet)) throw new Exception("Column [contextId] not available while pulling data");
		$this->contextId = $resultSet["contextId"];
		if (! array_key_exists("symbol", $resultSet)) throw new Exception("Column [symbol] not available while pulling data");
		$this->setSymbol($resultSet["symbol"]);
		if (! array_key_exists("_value", $resultSet)) throw new Exception("Column [_value] not available while pulling data");
		$this->setValue($resultSet["_value"]);
		if (! array_key_exists("extraFilter", $resultSet)) throw new Exception("Column [extraFilter] not available while pulling data");
		$this->setExtraFilter($resultSet["extraFilter"]);
		if (! array_key_exists("extraInformation", $resultSet)) throw new Exception("Column [extraInformation] not available while pulling data");
		$this->setExtraInformation($resultSet["extraInformation"]);
		if (! array_key_exists("flags", $resultSet)) throw new Exception("Column [flags] not available while pulling data");
		$this->setFlags($resultSet["flags"]);
		$this->clearUpdateList();
	}
	public function getId() { return md5($this->contextId); }
	public function getIdWhereClause() { return "{ \"contextId\" : $this->contextId }"; }
	public function getId0()  { return $this->contextId; }
	public function getId0WhereClause()  { return "{ \"contextId\" : $this->contextId }"; }
	public function getContextId(){
		return $this->contextId;
	}
	public function setSymbol($symbol){
		$regex = self::getRegularExpression('symbol');
		if (! (is_null($regex) || preg_match($regex['rule'], $symbol) === 1)) throw new Exception("[ symbol ] : ".$regex['message']);
		$this->symbol = $symbol;
		$this->addToUpdateList("symbol", $symbol);
		return $this;
	}
	public function getSymbol(){
		return $this->symbol;
	}
	public function setValue($value){
		$regex = self::getRegularExpression('value');
		if (! (is_null($regex) || preg_match($regex['rule'], $value) === 1)) throw new Exception("[ value ] : ".$regex['message']);
		$this->value = $value;
		$this->addToUpdateList("_value", $value);
		return $this;
	}
	public function getValue(){
		return $this->value;
	}
	public function setExtraFilter($extraFilter){
		$regex = self::getRegularExpression('extraFilter');
		if (! (is_null($regex) || preg_match($regex['rule'], $extraFilter) === 1)) throw new Exception("[ extraFilter ] : ".$regex['message']);
		$this->extraFilter = $extraFilter;
		$this->addToUpdateList("extraFilter", $extraFilter);
		return $this;
	}
	public function getExtraFilter(){
		return $this->extraFilter;
	}
	public function setExtraInformation($extraInformation){
		$regex = self::getRegularExpression('extraInformation');
		if (! (is_null($regex) || preg_match($regex['rule'], $extraInformation) === 1)) throw new Exception("[ extraInformation ] : ".$regex['message']);
		$this->extraInformation = $extraInformation;
		$this->addToUpdateList("extraInformation", $extraInformation);
		return $this;
	}
	public function getExtraInformation(){
		return $this->extraInformation;
	}
	public function setFlags($flags){
		$regex = self::getRegularExpression('flags');
		if (! (is_null($regex) || preg_match($regex['rule'], $flags) === 1)) throw new Exception("[ flags ] : ".$regex['message']);
		$this->flags = $flags;
		$this->addToUpdateList("flags", $flags);
		return $this;
	}
	public function getFlags(){
		return $this->flags;
	}
	public static function getId0Columnname()   { return "contextId"; }
	public static function getIdColumnnames() { return array("contextId"); }
	public static function getRegularExpression($colname)   {
		$tArray1 = array();
		$regexArray1 = null;
		if (isset($tArray1[$colname])) $regexArray1 = $tArray1[$colname];
		return $regexArray1;
	}
	public function getMyClassname()    { return self::getClassname(); }
	public function getMyTablename()    { return self::getTablename(); }
	public static function getClassname()  { return "ContextLookup"; }
	public static function getTablename()  { return "_contextLookup"; }
	public static function column2Property($colname)    {
		$tArray1 = array(
			"contextId" => "contextId"
			, "symbol" => "symbol"
			, "_value" => "value"
			, "extraFilter" => "extraFilter"
			, "extraInformation" => "extraInformation"
			, "flags" => "flags"
		);
		$pname = null;
		if (isset($tArray1[$colname])) $pname = $tArray1[$colname];
		return $pname;
	}
	public static function property2Column($pname)    {
		$tArray1 = array(
			"contextId" => "contextId"
			, "symbol" => "symbol"
			, "value" => "_value"
			, "extraFilter" => "extraFilter"
			, "extraInformation" => "extraInformation"
			, "flags" => "flags"
		);
		$colname = null;
		if (isset($tArray1[$pname])) $colname = $tArray1[$pname];
		return $colname;
	}
	public static function getColumnLookupTable()   {
		$tArray1 = array();
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "contextId";		$tArray1[$tsize]['pname'] = "contextId";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "symbol";		$tArray1[$tsize]['pname'] = "symbol";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "_value";		$tArray1[$tsize]['pname'] = "value";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "extraFilter";		$tArray1[$tsize]['pname'] = "extraFilter";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "extraInformation";		$tArray1[$tsize]['pname'] = "extraInformation";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "flags";		$tArray1[$tsize]['pname'] = "flags";
		return $tArray1;
	}
}
?>