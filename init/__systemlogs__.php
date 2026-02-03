<?php
/******************************************************
**                                                   **
**              CLASSNAME : SystemLogs               **
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
class SystemLogs extends __data__ {
	protected $database;
	protected $conn;
	private $logId;
	private $logDate;
	private $username;
	private $contextPosition;
	private $opname;
	private $target;
	private $flags;
/*BEGIN OF CUSTOM CODES : You should Add Your Custom Codes Below this line*/
    public final static function addLog2($conn, $datestring, $username, $opname, $target, $rollback = true)	{
		//Get contextPositionId from op
        $contextPositionId = ContextPosition::getContextIdFromName("De Morgan", $opname, $conn);
        //$query = "{ \"query\" : \"insert\", \"table\" : \"_systemlogs\", \"cols\" : [ { \"logDate\" : \"'$datestring'\" }, { \"username\" : \"'$username'\" }, { \"contextPosition\" : \"'$contextPositionId'\" }, { \"target\" : \"'$target'\"} ] }";
		$query = SimpleQueryBuilder::buildInsert("_systemlogs", array("logDate" => $datestring, "username" => $username, "contextPosition" => $contextPositionId, "opname" => $opname, "target" => $target));
		$jresult1 = SQLEngine::execute($query, $conn, $rollback);
        if (is_null($jresult1)) throw new Exception("Malformed Query");
        $jArray1 = json_decode($jresult1, true);
        if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
	}
	public final static function addLog($config1 /* :ConfigurationData */, $datestring, $username, $opname, $target)  {
        //$opname is is a ContextPosition::cName like , manage_login
        $host = $config1->getHostname();
        $dbname = $config1->getDatabase();
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
        self::addLog2($conn, $datestring, $username, $opname, $target, true);
        $conn = null;
    }
/*END OF CUSTOM CODES : You should Add Your Custom Codes Above this line*/
	public static function create($database, $id, $conn) { return new SystemLogs($database, $id, $conn); }
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
		if (! array_key_exists("logId", $resultSet)) throw new Exception("Column [logId] not available while pulling data");
		$this->logId = $resultSet["logId"];
		if (! array_key_exists("logDate", $resultSet)) throw new Exception("Column [logDate] not available while pulling data");
		$this->setLogDate($resultSet["logDate"]);
		if (! array_key_exists("username", $resultSet)) throw new Exception("Column [username] not available while pulling data");
		$this->setUsername($resultSet["username"]);
		if (! array_key_exists("contextPosition", $resultSet)) throw new Exception("Column [contextPosition] not available while pulling data");
		$this->setContextPosition($resultSet["contextPosition"]);
		if (! array_key_exists("opname", $resultSet)) throw new Exception("Column [operationName] not available while pulling data");
		$this->setOperationName($resultSet["opname"]);
		if (! array_key_exists("target", $resultSet)) throw new Exception("Column [target] not available while pulling data");
		$this->setTarget($resultSet["target"]);
		if (! array_key_exists("flags", $resultSet)) throw new Exception("Column [flags] not available while pulling data");
		$this->setFlags($resultSet["flags"]);
		$this->clearUpdateList();
	}
	public function getId() { return md5($this->logId); }
	public function getIdWhereClause() { return "{ \"logId\" : $this->logId }"; }
	public function getId0()  { return $this->logId; }
	public function getId0WhereClause()  { return "{ \"logId\" : $this->logId }"; }
	public function getLogId(){
		return $this->logId;
	}
	public function setLogDate($logDate){
		$regex = self::getRegularExpression('logDate');
		if (! (is_null($regex) || preg_match($regex['rule'], $logDate) === 1)) throw new Exception("[ logDate ] : ".$regex['message']);
		if (is_null($logDate)) return $this;
		$this->logDate = new DateAndTime($logDate);
		$this->addToUpdateList("logDate", $logDate);
		return $this;
	}
	public function getLogDate(){
		return $this->logDate;
	}
	public function setUsername($username){
		$regex = self::getRegularExpression('username');
		if (! (is_null($regex) || preg_match($regex['rule'], $username) === 1)) throw new Exception("[ username ] : ".$regex['message']);
		$this->username = $username;
		$this->addToUpdateList("username", $username);
		return $this;
	}
	public function getUsername(){
		return $this->username;
	}
	public function setContextPosition($contextId){
		$regex = self::getRegularExpression('contextPosition');
		if (! (is_null($regex) || preg_match($regex['rule'], $contextId) === 1)) throw new Exception("[ contextPosition ] : ".$regex['message']);
		if (is_null($contextId)) return $this;
		$this->contextPosition = new ContextPosition($this->database, $contextId, $this->conn);
		$this->addToUpdateList("contextPosition", $contextId);
		return $this;
	}
	public function getContextPosition(){
		return $this->contextPosition;
	}
	public function setOperationName($opname){
		$regex = self::getRegularExpression('opname');
		if (! (is_null($regex) || preg_match($regex['rule'], $opname) === 1)) throw new Exception("[ operationName ] : ".$regex['message']);
		$this->opname = $opname;
		$this->addToUpdateList("opname", $opname);
		return $this;
	}
	public function getOperationName()	{
		return $this->opname;
	}
	public function setTarget($target){
		$regex = self::getRegularExpression('target');
		if (! (is_null($regex) || preg_match($regex['rule'], $target) === 1)) throw new Exception("[ target ] : ".$regex['message']);
		$this->target = $target;
		$this->addToUpdateList("target", $target);
		return $this;
	}
	public function getTarget(){
		return $this->target;
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
	public static function getId0Columnname()   { return "logId"; }
	public static function getIdColumnnames() { return array("logId"); }
	public static function getRegularExpression($colname)   {
		$tArray1 = array();
		$regexArray1 = null;
		if (isset($tArray1[$colname])) $regexArray1 = $tArray1[$colname];
		return $regexArray1;
	}
	public function getMyClassname()    { return self::getClassname(); }
	public function getMyTablename()    { return self::getTablename(); }
	public static function getClassname()  { return "SystemLogs"; }
	public static function getTablename()  { return "_systemlogs"; }
	public static function column2Property($colname)    {
		$tArray1 = array(
			"logId" => "logId"
			, "logDate" => "logDate"
			, "username" => "username"
			, "contextPosition" => "contextPosition"
			, "target" => "target"
			, "flags" => "flags"
		);
		$pname = null;
		if (isset($tArray1[$colname])) $pname = $tArray1[$colname];
		return $pname;
	}
	public static function property2Column($pname)    {
		$tArray1 = array(
			"logId" => "logId"
			, "logDate" => "logDate"
			, "username" => "username"
			, "contextPosition" => "contextPosition"
			, "target" => "target"
			, "flags" => "flags"
		);
		$colname = null;
		if (isset($tArray1[$pname])) $colname = $tArray1[$pname];
		return $colname;
	}
	public static function getColumnLookupTable()   {
		$tArray1 = array();
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "logId";		$tArray1[$tsize]['pname'] = "logId";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "logDate";		$tArray1[$tsize]['pname'] = "logDate";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "username";		$tArray1[$tsize]['pname'] = "username";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "contextPosition";		$tArray1[$tsize]['pname'] = "contextPosition";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "target";		$tArray1[$tsize]['pname'] = "target";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "flags";		$tArray1[$tsize]['pname'] = "flags";
		return $tArray1;
	}
}
?>