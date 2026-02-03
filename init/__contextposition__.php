<?php
/******************************************************
**                                                   **
**            CLASSNAME : ContextPosition            **
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
class ContextPosition extends __data__ {
	protected $database;
	protected $conn;
	private $contextId;
	private $contextName;
	private $characterPosition;
	private $caption;
	private $extraFilter;
	private $extraInformation;
	private $flags;
	public static $__ALLOW = 1;
	public static $__DENY = 0;
	public static $__DONOTCARE = 2;
	public static $__ALLOW_ALL = 1;
	public static $__DENY_ALL = 0;
	public static $__DONOTCARE_ALL = 2;
	public static $__CUSTOMIZE = 4;
/*BEGIN OF CUSTOM CODES : You should Add Your Custom Codes Below this line*/
    public final static function getContextIdFromName($database, $name, $conn)  {
        //$query = "{ \"query\" : \"select\", \"tables\" : [ { \"table\" : \"_contextPosition\"} ], \"cols\" : [ { \"col\" : \"cId\" } ], \"where\" : { \"cName\" : \"'$name'\" } }";
		$query = SimpleQueryBuilder::buildSelect(array("_contextPosition"), array("cId"), array("cName" => $name));
		$jresult1 = SQLEngine::execute($query, $conn);
        if (is_null($jresult1)) throw new Exception("Malformed Query");
        $jArray1 = json_decode($jresult1, true);
        if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
		if ($jArray1['count'] !== 1) throw new Exception("Duplicate or no record found");
        $resultSet = $jArray1['rows'][0];
        if (! array_key_exists("cId", $resultSet)) throw new Exception("Column [cId] not available while pulling data");
        return $resultSet['cId'];
    }
    public final static function getPositionFromName($database, $name, $conn)   {
        //$query = "{ \"query\" : \"select\", \"tables\" : [ { \"table\" : \"_contextPosition\"} ], \"cols\" : [ { \"col\" : \"cPosition\" } ], \"where\" : { \"cName\" : \"'$name'\" } }";
		$query = SimpleQueryBuilder::buildSelect(array("_contextPosition"), array("cPosition"), array("cName" => $name));
		$jresult1 = SQLEngine::execute($query, $conn);
        if (is_null($jresult1)) throw new Exception("Malformed Query");
        $jArray1 = json_decode($jresult1, true);
        if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
		if ($jArray1['count'] !== 1) throw new Exception("Duplicate or no record found");
        $resultSet = $jArray1['rows'][0];
        if (! array_key_exists("cPosition", $resultSet)) throw new Exception("Column [cPosition] not available while pulling data");
        return $resultSet['cPosition'];
    }
    public final static function getNameFromPosition($database, $pos, $conn)    {
        //$query = "{ \"query\" : \"select\", \"tables\" : [ { \"table\" : \"_contextPosition\"} ], \"cols\" : [ { \"col\" : \"cName\" } ], \"where\" : { \"cPosition\" : \"'$pos'\" } }";
		$query = SimpleQueryBuilder::buildSelect(array("_contextPosition"), array("cName"), array("cPosition" => $pos));
		$jresult1 = SQLEngine::execute($query, $conn);
        if (is_null($jresult1)) throw new Exception("Malformed Query");
        $jArray1 = json_decode($jresult1, true);
        if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
		if ($jArray1['count'] !== 1) throw new Exception("Duplicate or no record found");
        $resultSet = $jArray1['rows'][0];
        if (! array_key_exists("cName", $resultSet)) throw new Exception("Column [cName] not available while pulling data");
        return $resultSet['cName'];
    }
/*END OF CUSTOM CODES : You should Add Your Custom Codes Above this line*/
	public static function create($database, $id, $conn) { return new ContextPosition($database, $id, $conn); }
	public function __construct($database, $id, $conn)    {
		$this->database = $database;
		$this->conn = $conn;
		$tablename = self::getTablename();
		$whereClause = self::getId0Columnname();
		$whereClause = "{ \"".$whereClause."\" : $id }";
		$query = "{\"query\" : \"select\", \"tables\": [ { \"table\" : \"".$tablename."\" } ], \"cols\" : [ { \"col\" : \"*\" } ], \"where\" : $whereClause }";
		$jresult1 = SQLEngine::execute($query, $conn);
		$jArray1 = json_decode($jresult1, true);
		if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
		if ($jArray1['count'] !== 1) throw new Exception("Duplicate or no record found");
		$resultSet = $jArray1['rows'][0];
		if (! array_key_exists("cId", $resultSet)) throw new Exception("Column [cId] not available while pulling data");
		$this->contextId = $resultSet["cId"];
		if (! array_key_exists("cName", $resultSet)) throw new Exception("Column [cName] not available while pulling data");
		$this->setContextName($resultSet["cName"]);
		if (! array_key_exists("cPosition", $resultSet)) throw new Exception("Column [cPosition] not available while pulling data");
		$this->setCharacterPosition($resultSet["cPosition"]);
		if (! array_key_exists("caption", $resultSet)) throw new Exception("Column [caption] not available while pulling data");
		$this->setCaption($resultSet["caption"]);
		if (! array_key_exists("extraFilter", $resultSet)) throw new Exception("Column [extraFilter] not available while pulling data");
		$this->setExtraFilter($resultSet["extraFilter"]);
		if (! array_key_exists("extraInformation", $resultSet)) throw new Exception("Column [extraInformation] not available while pulling data");
		$this->setExtraInformation($resultSet["extraInformation"]);
		if (! array_key_exists("flags", $resultSet)) throw new Exception("Column [flags] not available while pulling data");
		$this->setFlags($resultSet["flags"]);
		$this->clearUpdateList();
	}
	public function getId() { return md5($this->contextId); }
	public function getIdWhereClause() { return "{ \"cId\" : $this->contextId }"; }
	public function getId0()  { return $this->contextId; }
	public function getId0WhereClause()  { return "{ \"cId\" : $this->contextId }"; }
	public function getContextId(){
		return $this->contextId;
	}
	public function setContextName($contextName){
		$regex = self::getRegularExpression('contextName');
		if (! (is_null($regex) || preg_match($regex['rule'], $contextName) === 1)) throw new Exception("[ contextName ] : ".$regex['message']);
		$this->contextName = $contextName;
		$this->addToUpdateList("cName", $contextName);
		return $this;
	}
	public function getContextName(){
		return $this->contextName;
	}
	public function setCharacterPosition($characterPosition){
		$regex = self::getRegularExpression('characterPosition');
		if (! (is_null($regex) || preg_match($regex['rule'], $characterPosition) === 1)) throw new Exception("[ characterPosition ] : ".$regex['message']);
		$this->characterPosition = $characterPosition;
		$this->addToUpdateList("cPosition", $characterPosition);
		return $this;
	}
	public function getCharacterPosition(){
		return $this->characterPosition;
	}
	public function setCaption($caption){
		$regex = self::getRegularExpression('caption');
		if (! (is_null($regex) || preg_match($regex['rule'], $caption) === 1)) throw new Exception("[ caption ] : ".$regex['message']);
		$this->caption = $caption;
		$this->addToUpdateList("caption", $caption);
		return $this;
	}
	public function getCaption(){
		return $this->caption;
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
	public static function getId0Columnname()   { return "cId"; }
	public static function getIdColumnnames() { return array("cId"); }
	public static function getRegularExpression($colname)   {
		$tArray1 = array();
		$regexArray1 = null;
		if (isset($tArray1[$colname])) $regexArray1 = $tArray1[$colname];
		return $regexArray1;
	}
	public function getMyClassname()    { return self::getClassname(); }
	public function getMyTablename()    { return self::getTablename(); }
	public static function getClassname()  { return "ContextPosition"; }
	public static function getTablename()  { return "_contextPosition"; }
	public static function column2Property($colname)    {
		$tArray1 = array(
			"cId" => "contextId"
			, "cName" => "contextName"
			, "cPosition" => "characterPosition"
			, "caption" => "caption"
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
			"contextId" => "cId"
			, "contextName" => "cName"
			, "characterPosition" => "cPosition"
			, "caption" => "caption"
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
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "cId";		$tArray1[$tsize]['pname'] = "contextId";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "cName";		$tArray1[$tsize]['pname'] = "contextName";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "cPosition";		$tArray1[$tsize]['pname'] = "characterPosition";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "caption";		$tArray1[$tsize]['pname'] = "caption";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "extraFilter";		$tArray1[$tsize]['pname'] = "extraFilter";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "extraInformation";		$tArray1[$tsize]['pname'] = "extraInformation";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "flags";		$tArray1[$tsize]['pname'] = "flags";
		return $tArray1;
	}
}
?>