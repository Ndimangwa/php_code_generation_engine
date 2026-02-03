<?php
/******************************************************
**                                                   **
**            CLASSNAME : NavigationMenu             **
**  Copyright (c) Zoomtong Company Limited           **
**  Developed by : Ndimangwa Fadhili Ngoya           **
**  Timestamp    : 2021:08:15:15:02:12               **
**  Phones       : +255 787 101 808 / 762 357 596    **
**  Email        : ndimangwa@gmail.com               **
**  Address      : P.O BOX 7436 MOSHI, TANZANIA      **
**                                                   **
**  Dedication to my dear wife Valentina             **
**                my daughters Raheli & Keziah       **
**                                                   **
*******************************************************/
class NavigationMenu extends __data__ {
	protected $database;
	protected $conn;
	private $menuId;
	private $menuName;
	private $pageToGo;
	private $sequenceNumber;
	private $parentMenu;
	private $contextName;
	private $flags;
/*BEGIN OF CUSTOM CODES : You should Add Your Custom Codes Below this line*/
	public static function loadMenu($conn, $nextPage, $parentMenu = null, $contextName = null)	{
		$pid = ""; 
		$whereArray1 = null;
		if (! is_null($parentMenu)) {
			$pid = "data-menu-pid=\"$parentMenu\"";
			$whereArray1 = array('pId' => $parentMenu);
		}
		$line = "<div class=\"ui-nav-menu\"><ul class=\"nav nav-pills mb-3 nav-custom-menu\" $pid>";
		$jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
			array('_navigationMenu'),
			array('*'),
			$whereArray1
		),$conn);
		if (is_null($jresult1)) throw new Exception("Could not fetch results");
		$jArray1 = json_decode($jresult1, true);
		if (is_null($jArray1)) throw new Exception("Could not decode results");
		if ($jArray1['code'] != 0) throw new Exception($jArray1['message']);
		foreach ($jArray1['rows'] as $row)	{
			$menuId = $row['menuId'];
			$menuName = $row['menuName'];
			$pageToGo = $row['pageToGo'];
			$seqno = $row['seqno'];
			$pId = $row['pId'];
			$cName = $row['cName'];
			//Constructing line 
			$active = ""; $blnSelected = "false"; if ($contextName == $cName) { 
				$active = "active";  $blnSelected = "true";
			}
			$href = $nextPage;
			if (! is_null($pageToGo) && $pageToGo != "")	{
				$href .= "&".$pageToGo;
			}
			$href .= "&parentMenu=$menuId";
			$line .= "<li class=\"nav-item\" data-menu-id=\"$menuId\"><a class=\"nav-link $active\" data-toggle=\"pill\" role=\"tab\" aria-selected=\"$blnSelected\" href=\"$href\">$menuName</a></li>";
		}
		$line .= "</ul></div>";
		return $line;
	}
/*END OF CUSTOM CODES : You should Add Your Custom Codes Above this line*/
	public static function create($database, $id, $conn) { return new NavigationMenu($database, $id, $conn); }
	public function __construct($database, $id, $conn)    {
		$this->setMe($database, $id, $conn);
	}
	public function setMe($database, $id, $conn)    {
		$this->database = $database;
		$this->conn = $conn;
		$whereClause = self::getId0Columnname();
		$whereClause = array($whereClause => $id);
		$query = SimpleQueryBuilder::buildSelect(array(self::getTablename()), array('*'), $whereClause);
		$jresult1 = SQLEngine::execute($query, $conn);
		$jArray1 = json_decode($jresult1, true);
		if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
		if ($jArray1['count'] !== 1) throw new Exception("Duplicate or no record found");
		$resultSet = $jArray1['rows'][0];
		if (! array_key_exists("menuId", $resultSet)) throw new Exception("Column [menuId] not available while pulling data");
		$this->menuId = $resultSet["menuId"];
		if (! array_key_exists("menuName", $resultSet)) throw new Exception("Column [menuName] not available while pulling data");
		$this->setMenuName($resultSet["menuName"]);
		if (! array_key_exists("pageToGo", $resultSet)) throw new Exception("Column [pageToGo] not available while pulling data");
		$this->setPageToGo($resultSet["pageToGo"]);
		if (! array_key_exists("seqno", $resultSet)) throw new Exception("Column [seqno] not available while pulling data");
		$this->setSequenceNumber($resultSet["seqno"]);
		if (! array_key_exists("pId", $resultSet)) throw new Exception("Column [pId] not available while pulling data");
		$this->setParentMenu($resultSet["pId"]);
		if (! array_key_exists("cName", $resultSet)) throw new Exception("Column [cName] not available while pulling data");
		$this->setContextName($resultSet["cName"]);
		if (! array_key_exists("flags", $resultSet)) throw new Exception("Column [flags] not available while pulling data");
		$this->setFlags($resultSet["flags"]);
		$this->clearUpdateList();
		return $this;
	}
	public static function loadAllData($__conn) {
		$colArray1 = array('menuId', 'menuName');
		$query = SimpleQueryBuilder::buildSelect(array(self::getTablename()), $colArray1, null);
		$jresult1 = SQLEngine::execute($query, $__conn);
		$jArray1 = json_decode($jresult1, true);
		if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
		$dataArray1 = array();
		foreach ($jArray1['rows'] as $resultSet)    {
			$index = sizeof($dataArray1); $dataArray1[$index] = array();
			$dataArray1[$index]['__id__'] = $resultSet['menuId'];
			$myval = "";
			$myval .= " ".$resultSet['menuName'];
			$dataArray1[$index]['__name__'] = trim($myval);
		}
		return $dataArray1;
	}
	public function getId() { return md5($this->menuId); }
	public function getIdWhereClause() { return "{ \"menuId\" : $this->menuId }"; }
	public function getId0()  { return $this->menuId; }
	public function getId0WhereClause()  { return "{ \"menuId\" : $this->menuId }"; }
	public function getMenuId(){
		return $this->menuId;
	}
	public function setMenuName($menuName){
		$maxLength = self::getMaximumLength('menuName');
		if (! (is_null($maxLength) || ! (strlen($menuName) > $maxLength))) throw new Exception("[ menuName ($maxLength) ] : Data Length has exceeded the size");
		$regex = self::getRegularExpression('menuName');
		if (! (is_null($regex) || preg_match("/".$regex['rule']."/", $menuName) === 1)) throw new Exception("[ menuName ] : ".$regex['message']);
		$this->menuName = $menuName;
		$this->addToUpdateList("menuName", $menuName);
		return $this;
	}
	public function getMenuName(){
		return $this->menuName;
	}
	public function setPageToGo($pageToGo){
		$maxLength = self::getMaximumLength('pageToGo');
		if (! (is_null($maxLength) || ! (strlen($pageToGo) > $maxLength))) throw new Exception("[ pageToGo ($maxLength) ] : Data Length has exceeded the size");
		$regex = self::getRegularExpression('pageToGo');
		if (! (is_null($regex) || preg_match("/".$regex['rule']."/", $pageToGo) === 1)) throw new Exception("[ pageToGo ] : ".$regex['message']);
		$this->pageToGo = $pageToGo;
		$this->addToUpdateList("pageToGo", $pageToGo);
		return $this;
	}
	public function getPageToGo(){
		return $this->pageToGo;
	}
	public function setSequenceNumber($sequenceNumber){
		$maxLength = self::getMaximumLength('sequenceNumber');
		if (! (is_null($maxLength) || ! (strlen($sequenceNumber) > $maxLength))) throw new Exception("[ sequenceNumber ($maxLength) ] : Data Length has exceeded the size");
		$regex = self::getRegularExpression('sequenceNumber');
		if (! (is_null($regex) || preg_match("/".$regex['rule']."/", $sequenceNumber) === 1)) throw new Exception("[ sequenceNumber ] : ".$regex['message']);
		$this->sequenceNumber = $sequenceNumber;
		$this->addToUpdateList("seqno", $sequenceNumber);
		return $this;
	}
	public function getSequenceNumber(){
		return $this->sequenceNumber;
	}
	public function setParentMenu($menuId){
		$maxLength = self::getMaximumLength('parentMenu');
		if (! (is_null($maxLength) || ! (strlen($menuId) > $maxLength))) throw new Exception("[ parentMenu ($maxLength) ] : Data Length has exceeded the size");
		$regex = self::getRegularExpression('parentMenu');
		if (! (is_null($regex) || preg_match("/".$regex['rule']."/", $menuId) === 1)) throw new Exception("[ parentMenu ] : ".$regex['message']);
		if (is_null($menuId)) return $this;
		$this->parentMenu = new NavigationMenu($this->database, $menuId, $this->conn);
		$this->addToUpdateList("pId", $menuId);
		return $this;
	}
	public function getParentMenu(){
		return $this->parentMenu;
	}
	public function setContextName($contextName){
		$maxLength = self::getMaximumLength('contextName');
		if (! (is_null($maxLength) || ! (strlen($contextName) > $maxLength))) throw new Exception("[ contextName ($maxLength) ] : Data Length has exceeded the size");
		$regex = self::getRegularExpression('contextName');
		if (! (is_null($regex) || preg_match("/".$regex['rule']."/", $contextName) === 1)) throw new Exception("[ contextName ] : ".$regex['message']);
		$this->contextName = $contextName;
		$this->addToUpdateList("cName", $contextName);
		return $this;
	}
	public function getContextName(){
		return $this->contextName;
	}
	public function setFlags($flags){
		$maxLength = self::getMaximumLength('flags');
		if (! (is_null($maxLength) || ! (strlen($flags) > $maxLength))) throw new Exception("[ flags ($maxLength) ] : Data Length has exceeded the size");
		$regex = self::getRegularExpression('flags');
		if (! (is_null($regex) || preg_match("/".$regex['rule']."/", $flags) === 1)) throw new Exception("[ flags ] : ".$regex['message']);
		$this->flags = $flags;
		$this->addToUpdateList("flags", $flags);
		return $this;
	}
	public function getFlags(){
		return $this->flags;
	}
	public static function getId0Columnname()   { return "menuId"; }
	public static function getIdColumnnames() { return array("menuId"); }
	public static function getReferenceClass($pname)    {
		$tArray1 = array('parentMenu' => 'NavigationMenu');
		$refclass = null; if (isset($tArray1[$pname])) $refclass = $tArray1[$pname];
		return $refclass;
	}
	public static function getColumnType($pname)    {
		$tArray1 = array('menuId' => 'integer', 'menuName' => 'text', 'pageToGo' => 'text', 'sequenceNumber' => 'integer', 'parentMenu' => 'object', 'contextName' => 'text', 'flags' => 'integer');
		$type = null; if (isset($tArray1[$pname])) $type = $tArray1[$pname];
		return $type;
	}
	public static function getRegularExpression($colname)   {
		$tArray1 = array();
		$regexArray1 = null;
		if (isset($tArray1[$colname])) $regexArray1 = $tArray1[$colname];
		return $regexArray1;
	}
	public static function getMaximumLength($colname)    {
		$tArray1 = array();
		$tArray1['menuName'] = 255; 
		$tArray1['pageToGo'] = 255; 
		$tArray1['contextName'] = 56; 
		$length = null;
		if (isset($tArray1[$colname])) $length = $tArray1[$colname];
		return $length;
	}
	public function getMyClassname()    { return self::getClassname(); }
	public function getMyTablename()    { return self::getTablename(); }
	public function getMyId0Columnname()  { return self::getId0Columnname(); }
	public static function getClassname()  { return "NavigationMenu"; }
	public static function getTablename()  { return "_navigationMenu"; }
	public static function column2Property($colname)    {
		$tArray1 = array(
			"menuId" => "menuId"
			, "menuName" => "menuName"
			, "pageToGo" => "pageToGo"
			, "seqno" => "sequenceNumber"
			, "pId" => "parentMenu"
			, "cName" => "contextName"
			, "flags" => "flags"
		);
		$pname = null;
		if (isset($tArray1[$colname])) $pname = $tArray1[$colname];
		return $pname;
	}
	public static function property2Column($pname)    {
		$tArray1 = array(
			"menuId" => "menuId"
			, "menuName" => "menuName"
			, "pageToGo" => "pageToGo"
			, "sequenceNumber" => "seqno"
			, "parentMenu" => "pId"
			, "contextName" => "cName"
			, "flags" => "flags"
		);
		$colname = null;
		if (isset($tArray1[$pname])) $colname = $tArray1[$pname];
		return $colname;
	}
	public static function getColumnLookupTable()   {
		$tArray1 = array();
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "menuId";		$tArray1[$tsize]['pname'] = "menuId";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "menuName";		$tArray1[$tsize]['pname'] = "menuName";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "pageToGo";		$tArray1[$tsize]['pname'] = "pageToGo";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "seqno";		$tArray1[$tsize]['pname'] = "sequenceNumber";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "pId";		$tArray1[$tsize]['pname'] = "parentMenu";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "cName";		$tArray1[$tsize]['pname'] = "contextName";
		$tsize = sizeof($tArray1);		$tArray1[$tsize] = array();		$tArray1[$tsize]['colname'] = "flags";		$tArray1[$tsize]['pname'] = "flags";
		return $tArray1;
	}
	public static function columnTransitiveMap($pname)  {
		$tArray1 =  array('menuId' => 'menuId', 'menuName' => 'menuName', 'pageToGo' => 'pageToGo', 'sequenceNumber' => 'sequenceNumber', 'parentMenu' => array('NavigationMenu.menuName'), 'contextName' => 'contextName', 'flags' => 'flags');
		$pmap = null; if (isset($tArray1[$pname])) $pmap = $tArray1[$pname];
		return $pmap;
	}
	public static function getSearchableColumns()    {
		/* Will return list of Searchable Properties */
		return array('menuName', 'pageToGo', 'sequenceNumber', 'parentMenu', 'contextName', 'flags');
	}
	public static function getASearchUI($page, $listOfColumnsToDisplay, $optIndex = 0)    {
		$line = "";
		$mycolumnlist = json_encode($listOfColumnsToDisplay);
		$line .= "&lt;div class=&quot;container __ui_search_container__ py-2&quot;&gt;    &lt;div class=&quot;row&quot;&gt;";
		$line .= "&lt;div class=&quot;col-md-6 &quot;&gt;    &lt;form id=&quot;__delta_init_basic__&quot;&gt;        &lt;div class=&quot;input-group mb-3&quot;&gt;            &lt;input name=&quot;__ui_search_input__&quot; id=&quot;__ui_search_input__$optIndex&quot; type=&quot;search&quot; data-min-length=&quot;3&quot;                class=&quot;form-control&quot;required placeholder=&quot;Search&quot; aria-label=&quot;Search&quot; aria-describedby=&quot;basic-addon2&quot; /&gt;            &lt;div class=&quot;input-group-append&quot;&gt;                &lt;button id=&quot;__ui_search_button__$optIndex&quot; data-form-id=&quot;__delta_init_basic__&quot; data-output-target=&quot;__ui_search_output_target__$optIndex&quot;                    data-display-column='$mycolumnlist' data-error-target=&quot;__ui_search_error__$optIndex&quot;                    data-column='[&quot;menuName&quot;]' data-page='$page' data-class='NavigationMenu'                    class=&quot;btn btn-outline-primary btn-perform-search&quot; type=&quot;button&quot;      data-search-input=&quot;text&quot; data-search-input-id=&quot;__ui_search_input__$optIndex&quot; data-toggle=&quot;tooltip&quot;                    title=&quot;This is a basic search&quot;&gt;Search&lt;/button&gt;            &lt;/div&gt;        &lt;/div&gt;    &lt;/form&gt;&lt;/div&gt;";
		$line .= "&lt;div class=&quot;col-md-6 &quot;&gt;&lt;button type=&quot;button &quot;class=&quot;btn btn-outline-primary btn-block&quot; name=&quot;__ui_advanced_search_button__&quot; id=&quot;__ui_advanced_search_button__$optIndex&quot; data-output-target=&quot;__ui_search_output_target__$optIndex&quot; data-display-column='$mycolumnlist' data-error-target=&quot;__ui_search_error__$optIndex&quot; data-column='[&quot;menuId&quot;,&quot;menuName&quot;,&quot;pageToGo&quot;,&quot;sequenceNumber&quot;,&quot;parentMenu&quot;,&quot;contextName&quot;,&quot;flags&quot;]' data-min-length=&quot;3&quot; data-page='$page' data-class='NavigationMenu' data-search-dialog=&quot;__dialog_search_container_01__&quot; data-toggle=&quot;tooltip&quot; title=&quot;This is a more advanced search technique&quot;&gt;Advanced Search&lt;/button&gt;&lt;/div&gt;&lt;/div&gt;&lt;br/&gt;&lt;div id=&quot;__ui_search_error__$optIndex&quot; class=&quot;p-1 ui-sys-error-message&quot;&gt;&lt;/div&gt;&lt;div style=&quot;overflow-x: scroll;&quot; id=&quot;__ui_search_output_target__$optIndex&quot;&gt;&lt;/div&gt;&lt;/div&gt;";
		$line .= "&lt;script type=&quot;text/javascript&quot;&gt;(function($)    {    $(function()    {        var callbackFunction$optIndex = function(\$button1, data, textStatus, optionArgumentArray1) {            var \$dialog1 = $('#' + \$button1.data('searchDialog'));            \$dialog1 = showAdvancedSearchDialog(\$button1, \$dialog1, data, Constant);            \$dialog1.modal('show');      };        $('#__ui_advanced_search_button__$optIndex').on('click', function(e)   {            var \$button1 = $(this);            var columnList = \$button1.data('column');            var classname = \$button1.data('class');            var payload = { columns: columnList, classname: classname };            fSendAjax(\$button1,                    $('&lt;span/&gt;'),                    '../server/serviceGetAdvancedSearchPayload.php',                    payload,                    null,                    null,                    callbackFunction$optIndex,                    null,                    null,                    &quot;POST&quot;,                    true,                    false,                    &quot;Processing ....&quot;,                    null,                    null);        });    });})(jQuery);&lt;/script&gt;";
		return htmlspecialchars_decode($line);
	}
	public function getMyPropertyValue($pname)  {
		if ($pname == "menuId") return $this->menuId;
		else if ($pname == "menuName") return $this->menuName;
		else if ($pname == "pageToGo") return $this->pageToGo;
		else if ($pname == "sequenceNumber") return $this->sequenceNumber;
		else if ($pname == "parentMenu") return $this->parentMenu;
		else if ($pname == "contextName") return $this->contextName;
		else if ($pname == "flags") return $this->flags;
		return null;
	}
	public static function getValue0Columnname() {
		return "menuName";
	}
	public static function getValueColumnnames()   {
		return array('menuName');
	}
	public function getName() {
		return array($this->menuName);
	}
	public function getName0() {
		$namedValue = $this->menuName;
		return $namedValue;
	}
}
?>