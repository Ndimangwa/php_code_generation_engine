<?php 
class Authorize	{
/*
Requirements 
1. loginId, available through session 
2. Operation name, ie add users
3. type of operation
	{ normal, targetUserAttention, targetGroupAttention }
4. targetUserId [Option, if type of operation is targetUserAttention ]
5. targetGroupId [Option, if type of operation is targetGroupAttention ]
*/
	private static function makeHeaderDataStructure($ds1, $object1)	{
		$ds1['class'][sizeof($ds1['class'])] = $object1->getMyClassname();
		$ds1['classid'][sizeof($ds1['classid'])] = $object1->getId0();
		$ds1['caption'][sizeof($ds1['caption'])] = $object1->getName0();
		return $ds1;
	}
	public static function getAuthorizationGraphDataStructure($conn, $object1, $searchstring)	{
		/*
		return 	ds['class'][Login, JobTitle, Group, Group, ..System]
				ds['classid'][1,2,5,3,2,,...0]
				ds['caption']['Ndimangwa', 'CIO', ... 'System']
				ds['rows'][cId]['rule'] = ContextPosition::$__ALLOW  / ContextPosition::$__DENY
								['length'] = 2 //Ie 0-based , desition was made at which point 
		
		*/
		$ds1 = array();
		$ds1['class'] = array();
		$ds1['classid'] = array();
		$ds1['caption'] = array();
		$ds1['rows'] = array();
		$classname = $object1->getMyClassname();
		if (! in_array($classname, array('Login', 'JobTitle', 'Group'))) throw new Exception("[ $classname ] => Classname not in the allowed range");
		$tobject1 = $object1;
		//Preparing Headers 
		if ($classname == "Login")	{
			$ds1 = self::makeHeaderDataStructure($ds1, $tobject1);
			$ds1 = self::makeHeaderDataStructure($ds1, $tobject1->getJobTitle());
			$tobject1 = $tobject1->getGroup();
			while (! is_null($tobject1))	{
				$ds1 = self::makeHeaderDataStructure($ds1, $tobject1);
				$tobject1 = $tobject1->getParentGroup();
			}
		} else if ($classname == "JobTitle")	{
			$ds1 = self::makeHeaderDataStructure($ds1, $tobject1);
		} else if ($classname == "Group")	{
			while (! is_null($tobject1))	{
				$ds1 = self::makeHeaderDataStructure($ds1, $tobject1);
				$tobject1 = $tobject1->getParentGroup();
			}
		}
		//Now System Compile
		$ds1['class'][sizeof($ds1['class'])] = "System";
		$ds1['classid'][sizeof($ds1['classid'])] = 0;
		$ds1['caption'][sizeof($ds1['caption'])] = "System";
		//Last System 
		$jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
			array('_contextManager'),
			array('defaultXValue'),
			null
		), $conn);
		if (is_null($jresult1)) throw new Exception('Could not pull Search Results');
		$jArray1 = json_decode($jresult1, true);
		if (is_null($jArray1)) throw new Exception("Malformed Query Results");
		if ($jArray1['count'] != 1) throw new Exception('Last Resort for the System Must be Unique');
		$defaultAllow = ContextPosition::$__DENY; if ($jArray1['rows'][0]['defaultXValue'] == 1) $defaultAllow = ContextPosition::$__ALLOW;
		//Working with Data 
		$jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
			array('_contextPosition'),
			array('cId', 'cName', 'cPosition', 'caption'),
			array((JSON2SQL::$__OP_LIKE) => array("cName" => "%$searchstring%"))
		), $conn);
		if (is_null($jresult1)) throw new Exception("Could not pull Search Results");
		$jArray1 = json_decode($jresult1, true);
		if (is_null($jArray1)) throw new Exception("Malformed Query");
		if ($jArray1['count'] == 0) throw new Exception("Query Returned Empty Set");
		foreach ($jArray1['rows'] as $row)	{
			$tobject1 = $object1;
			$cId = $row['cId'];
			$cName = $row['cName'];
			$pos = $row['cPosition'];
			$caption = $row['caption'];
			$ds1['rows'][$cId] = array();
			$length = 0;
			if ($classname == "Login")	{
				$ctxChar = self::getContextCharacter("Ndimangwa", $conn, $tobject1->getContext(), $pos);
				if ($ctxChar != ContextPosition::$__DONOTCARE) {
					$ds1['rows'][$cId]['length'] = $length;
					$ds1['rows'][$cId]['rule'] = $ctxChar;
					continue;
				}
				$length++;
				$ctxChar = self::getContextCharacter("Ndimangwa", $conn, $tobject1->getJobTitle()->getContext(), $pos);
				if ($ctxChar != ContextPosition::$__DONOTCARE) {
					$ds1['rows'][$cId]['length'] = $length;
					$ds1['rows'][$cId]['rule'] = $ctxChar;
					continue;
				}
				$length++;
				$tobject1 = $tobject1->getGroup();
				$enableContinue = false;
				while(! is_null($tobject1))	{
					$ctxChar = self::getContextCharacter("Ndimangwa", $conn, $object1->getContext(), $pos);
					if ($ctxChar != ContextPosition::$__DONOTCARE) {
						$ds1['rows'][$cId]['length'] = $length;
						$ds1['rows'][$cId]['rule'] = $ctxChar;
						$enableContinue = true;
						continue;
					}
					$length++;
					$tobject1 = $tobject1->getParentGroup();
				}
				if ($enableContinue) continue;
			} else if ($classname == "JobTitle")	{
				$ctxChar = self::getContextCharacter("Ndimangwa", $conn, $tobject1->getContext(), $pos);
				if ($ctxChar != ContextPosition::$__DONOTCARE) {
					$ds1['rows'][$cId]['length'] = $length;
					$ds1['rows'][$cId]['rule'] = $ctxChar;
					continue;
				}
				$length++;
			} else if ($classname == "Group")	{
				$enableContinue = false;
				while(! is_null($tobject1))	{
					$ctxChar = self::getContextCharacter("Ndimangwa", $conn, $object1->getContext(), $pos);
					if ($ctxChar != ContextPosition::$__DONOTCARE) {
						$ds1['rows'][$cId]['length'] = $length;
						$ds1['rows'][$cId]['rule'] = $ctxChar;
						$enableContinue = true;
						continue;
					}
					$length++;
					$tobject1 = $tobject1->getParentGroup();
				}
				if ($enableContinue) continue;
			}
			//We need to Attach the Last Resourt
			$ds1['rows'][$cId]['length'] = $length; 
			$ds1['rows'][$cId]['rule'] = $defaultAllow;
			$length++;
		}
		return $ds1;
	}
	public final static function getAuthorizationGraphDataStructureOld($database, $conn, $object1, $objecttype, $searchstring)	{
		/*
		NOTE: Before calling this function, make sure the User is not Root
		INPUT:
		$object1 of type Student, User or Group [JobTitle is not supported as input]
		$objecttype user or group , note student is also a user since it supports both getJobTitle as well as getGroup 
		$searchstring , since we are going to traverse against contextPosition objects available in our database 
		
		OUTPUT:
		datastructure Two Dimension Array 
		<Empty>:Header1:Header2: .... :Headern [Headern-1 is for System and Headern is status 1 Accept 0 Reject]
		<Empty>:Caption1:Caption2:.....:Captionn [Additional Info for Headers]
		contextId:X:X:1..........:1
		contextId:0:.............:0
		contextId:X:X:X:X:X:U....:1
		...
		
		*/
		$ds1 = array();
		$ds1['header']=array();
		$ds1['caption']=array();
		$ds1['header'][0] = "";
		$ds1['caption'][0] = "";
		$group1 = null;
		//A. Preparing Headers 
		if ($objecttype=="login")	{
			$ds1['header'][1]=$object1->getFullname();
			$ds1['caption'][1]="Login";
			$ds1['header'][2]=$object1->getJobTitle()->getJobName();
			$ds1['caption'][2]="Job Title";
			$group1 = $object1->getGroup();
		} else if ($objecttype == "group")	{
			$group1 = $object1;
		} else 	{
			return null; 
		}
		//We are now dealing with group 
		while (! is_null($group1))	{
			$len = sizeof($ds1['header']);
			$ds1['header'][$len] = $group1->getGroupName();
			$ds1['caption'][$len] = "Group";
			$group1 = $group1->getParentGroup();
		} //end-while
		$len = sizeof($ds1['header']);
		$ds1['header'][$len] = "System";
		$ds1['caption'][$len] = "System";
		//Status 
		$len = sizeof($ds1['header']);
		$ds1['header'][$len]="";
		$ds1['caption'][$len]="";
		//Row Width//Number Of Columns 
		$numberOfColumns = sizeof($ds1['header']);
		//B. Dealing with Data 
		$row="row";
		$count = 0;
		//----
		$query = "{ \"query\" : \"select\", \"tables\" : [ { \"table\" : \"ContextPosition\" } ], \"cols\" : [ { \"col\" : \"cId\" } ] }";
		$jresult1 = SQLEngine::execute($query, $conn);
        if (is_null($jresult1)) throw new Exception("Malformed Query");
        $jArray1 = json_decode($jresult1, true);
        if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
		foreach ($jArray1['rows'] as $dataArray1)	{
			$contextId = $dataArray1['cId'];
			$context1 = null;
			$context1 = new ContextPosition($database, $contextId, $conn);
			/*
			
			NOTE: About Graph , I have to work again later, since the SearchMatrix is not yet implemented in this version
			*/
			$matrix1 = new SearchMatrix($searchstring);
			if ($context1->searchMatrix($matrix1)->evaluateResult())	{
				$pos = $context1->getCharacterPosition();
				$rowIndex=$row.$count;
				//$rowIndex="'".$row.$count."'";
				$count++;
				$ds1[$rowIndex] = array();
				$ds1[$rowIndex][0] = $context1->getContextId();
				$group1 = null;
				if ($objecttype == "login")	{
					$context = $object1->getContext();
					$ctxChar=self::getContextCharacter($database, $conn, $context, $pos);
					$ds1[$rowIndex][1] = $ctxChar;
					if ($ctxChar != ContextPosition::$__DONOTCARE)	{
						//Finalize this row 
						$ds1[$rowIndex][$numberOfColumns - 1] = $ctxChar; //Whether Allow or Deny
						continue;
					}
					//Proceed to JobTitle 
					$job1 = $object1->getJobTitle();
					$context = $job1->getContext();
					$ctxChar=self::getContextCharacter($database, $conn, $context, $pos);
					$ds1[$rowIndex][2] = $ctxChar;
					if ($ctxChar != ContextPosition::$__DONOTCARE)	{
						//Finalize this row 
						$ds1[$rowIndex][$numberOfColumns - 1] = $ctxChar;
						continue;
					}
					$group1 = $object1->getGroup();					
				} else if ($objecttype == "group")	{
					$group1 = $object1;
				} else	{
					return null;
				}
				$systemDefault = true;
				while (! is_null($group1))	{
					$len = sizeof($ds1[$rowIndex]);
					$context=$group1->getContext();
					$ctxChar=self::getContextCharacter($database, $conn, $context, $pos);
					$ds1[$rowIndex][$len] = $ctxChar;
					if ($ctxChar != ContextPosition::$__DONOTCARE)	{
						//Finalize this row 
						$ds1[$rowIndex][$numberOfColumns - 1] = $ctxChar;
						$systemDefault=false;
						break;
					}
					$group1 = $group1->getParentGroup();
				} //end-inner-while
				if ($systemDefault)	{
					//Load Default X Value 
					$query="SELECT defaultXValue FROM contextManager";
					$sysresult = mysql_db_query($database, $query, $conn) or die("Could not load default security context");
					if (mysql_num_rows($sysresult) != 1) die("Problem with System security context");
					list($defaultSystemContext)=mysql_fetch_row($sysresult);
					$ds1[$rowIndex][$numberOfColumns-2] = intval($defaultSystemContext);
					$ds1[$rowIndex][$numberOfColumns-1] = intval($defaultSystemContext);
				}
			} //end-if-searchtext
		}//end-of-while
		return $ds1;
	}
	public final static function isSessionSet()	{
		return isset($_SESSION['auth']);
	}
	public final static function setSession($op)	{
		/* This will keep track of which operation has been denied */
		$_SESSION['auth']=$op;
	}
	public final static function getSessionValue()	{ return $_SESSION['auth']; }
	public final static function clearSession()	{
		/* Immediately after display an error message clear this message */
		unset($_SESSION['auth']);
	}
	private static function buildTheEntireContextValue($value, $offsetArr1)	{
		$valArray1 = str_split($value);
		$len = sizeof($valArray1);
		if ($len != 4) __object__::shootException("Block size should be four");
		$newValue = "";
		for ($i=0; $i < $len; $i++)	{
			$tval = $valArray1[$i];
			if (isset($offsetArr1[$i])) $tval = $offsetArr1[$i];
			$newValue .= $tval;
		}
		return $newValue;
	}
	private static function buildContextValue($value, $char, $offsetPos)	{
		$valArray1 = str_split($value);
		$len = sizeof($valArray1);
		if ($len != 4) __object__::shootException("Block size should be four");
		$newValue = "";
		for ($i =0; $i < $offsetPos; $i++)	$newValue .= $valArray1[$i];
		$newValue .= $char;
		for ($i = $offsetPos + 1; $i < $len; $i++) $newValue .= $valArray1[$i];
		return $newValue;
	}
	public final static function buildTheEntireContextString($database, $conn, $context, $positionalArray)	{
		//positionalArray of the form array[pos] = value [value can be 0, 1 or 2]
		if (is_null($positionalArray)) __object__::shootException("Positional Array Can not be Null");
		//Putting Array Properly 
		//positialArray[i] = val , to posiArr1[actualSymbolPos][actualSymbolOffset] = val 
		$posiArr1 = array();
		foreach ($positionalArray as $pos => $value)	{
			$actualSymbolPos = floor($pos / 4);
			$actualSymbolOffset = ($pos % 4);
			//Now build array 
			if (! isset($posiArr1[$actualSymbolPos])) {
				$posiArr1[$actualSymbolPos] = array();
			}
			$posiArr1[$actualSymbolPos][$actualSymbolOffset] = $value;
		}
		//Now I am having my datastructure let us move now 
		$contextArray1 = str_split($context); //Keep modifying contextArray
		foreach ($posiArr1 as $actualSymbolPos => $offsetArr1)	{
			$symbolToReplace = substr($context, $actualSymbolPos, 1);
			//We are required to build a new symbol 
			$valueOfSymbol = ContextLookup::getValueFromSymbol($database, $conn, $symbolToReplace);
			if (strlen($valueOfSymbol) != 4) __object__::shootException("Block size should be four");
			//Replace Now --- The entire block 
			$valueOfSymbol = self::buildTheEntireContextValue($valueOfSymbol, $offsetArr1);
			if (strlen($valueOfSymbol) != 4) __object__::shootException("Block size should be four");
			//Get New Symbol Now 
			$replacedSymbol = ContextLookup::getSymbolFromValue($database, $conn, $valueOfSymbol);
			//Now replace Now 
			$contextArray1[$actualSymbolPos] = $replacedSymbol;
		}
		//Now rewrite 
		$newContext = "";
		for ($i=0; $i < sizeof($contextArray1); $i++) $newContext .= $contextArray1[$i];
		if (strlen($newContext) != strlen($context)) __object__::shootException("Context Length deviation have been detected");
		return $newContext;
	}
	public final static function buildAllContextStringTo($database, $conn, $context, $action)	{
		//Note : Action can be ContextPosition::$__ALLOW/ __DENY / __DONOTCARE 
		//build value from action 
		$value = $action;
		$value .= "".$action;
		$value .= "".$action;
		$value .= "".$action;
		//Now we have ie "2222"
		$symbol = ContextLookup::getSymbolFromValue($database, $conn, $value);
		//Build a New ContextValue with the similar length as original 
		$newContext = "";
		$len = strlen($context);
		for ($i=0; $i < $len; $i++)	$newContext .= $symbol;
		if (strlen($newContext) != strlen($context)) __object__::shootException("Context Length deviation have been detected");
		return $newContext;
	}
	public final static function buildContextString($database, $conn, $context, $char, $pos)	{
		//context is the current context , 
		//char , can be either 0,1 or 2
		//pos from 0 to 1023
		$actualSymbolPos = floor($pos / 4);
		$actualSymbolOffset = ($pos % 4);
		//Get Symbol which will be replaced 
		$symbolToReplace = substr($context, $actualSymbolPos, 1);
		//We are now required to build a new symbol 
		$valueOfSymbol = ContextLookup::getValueFromSymbol($database, $conn, $symbolToReplace);
		if (strlen($valueOfSymbol) != 4) __object__::shootException("Block size should be four");
		//Replace Now
		$valueOfSymbol = self::buildContextValue($valueOfSymbol, $char, $actualSymbolOffset);
		if (strlen($valueOfSymbol) != 4) __object__::shootException("Block size should be four");
		//Get New Symbol Now 
		$replacedSymbol = ContextLookup::getSymbolFromValue($database, $conn, $valueOfSymbol);
		//Now we need to replace the symbol 
		$newContext = "";
		$contextArr1 = str_split($context);
		$len = sizeof($contextArr1);
		for ($i = 0; $i < $actualSymbolPos; $i++) $newContext .= $contextArr1[$i];
		$newContext .= $replacedSymbol;
		for ($i = $actualSymbolPos + 1; $i < $len; $i++) $newContext .= $contextArr1[$i];
		if (strlen($newContext) != strlen($context)) __object__::shootException("Context Length deviation have been detected");
		return $newContext;
	}
	public final static function getContextCharacter($database, $conn, $context, $pos)	{
		//context in the form of string of symbols 
		//pos from 0 to 1023
		$actualSymbolPos = floor($pos / 4);
		$actualSymbolOffset = ($pos % 4);
		if ($actualSymbolPos >= strlen($context)) __object__::shootException("Context Character, Array out of Bound");
		$symbolToLook = substr($context, $actualSymbolPos, 1);
		//GetString Value of the symbol 
		$valueOfSymbol = ContextLookup::getValueFromSymbol($database, $conn, $symbolToLook);
		if (strlen($valueOfSymbol) != 4) __object__::shootException("Block size should be four");
		//Now get Actual Character , you are now having 4-length string ie 0102
		return intval(substr($valueOfSymbol, $actualSymbolOffset, 1)); //will return 0,1 or 2
	}
	public final static function getGroupContextCharacter($database, $conn, $group1, $pos)	{
		if (is_null($group1)) return ContextLookup::$__DONOTCARE; /* Simply do not care we have reached top of the ladder and still we are facing do not care */
		$groupContext1=$group1->getContext();
		$groupContextChar1=self::getContextCharacter($database, $conn, $groupContext1, $pos);
		if ($groupContextChar1 !== ContextLookup::$__DONOTCARE) return $groupContextChar1;
		/* We still have X */
		return self::getGroupContextCharacter($database, $conn, $group1->getParentGroup(), $pos);
	}
	public final static function isAllowable2($conn /* ::ConfigurationData */, $opname /*manageprofile_edit*/, $optype = "normal", $setlog = "donotsetlog", $targetLoginId = null, $targetGroupId = null)	{
		$accept = false;
		$dbname = "Not-Applicable";
		$login1 = new Login($dbname, $_SESSION['login'][0]['id'], $conn);
		/*Make sure you do not lock yourself out if you have identified yourself*/
		if ($optype == "targetLoginAttention")	{
			if ($login1->getLoginId() == $targetLoginId)	{
				$conn = null;
				if ($setlog == "setlog") self::setSession($opname);
				return false;
			}
		}
		//1: Marked as Root 
		if ($login1->isRoot())	{ $conn = null; return true; }
		//2: position for this operation 
		$pos=ContextPosition::getPositionFromName($dbname, $opname, $conn); /* Mark this position */
		//3.1: Load System Security Context --- No need , we plug directly to the code
		//3.2 context value of this user
		$loginContext1 = $login1->getContext();
		/* 3.3 Extract character from context for position pos */
		$loginContextChar1 = self::getContextCharacter($dbname, $conn, $loginContext1, $pos);
		/*3.4 context character is 2*/	
		if ($loginContextChar1 == ContextLookup::$__DONOTCARE)	{    
			//Next-Level is Job Title
			$job1 = $login1->getJobTitle();
			/* context value of this job */
			$jobContext1 = $job1->getContext();
			/* context character for this job */
			$jobContextChar1 = self::getContextCharacter($dbname, $conn, $jobContext1, $pos);
			if ($jobContextChar1 == ContextLookup::$__DONOTCARE)	{
				//Group Traversal now
				$group1 = $login1->getGroup();
				$groupContextChar1 = self::getGroupContextCharacter($dbname, $conn, $group1, $pos);
				if ($groupContextChar1 == ContextLookup::$__DONOTCARE)	{
					//We need to consider the system default 
					$accept = ContextManager::isSystemDefaultAllowed($dbname, $conn);
				} else if ($groupContextChar1 == ContextLookup::$__DENY)	{
					$accept = false;
				} else if ($groupContextChar1 == ContextLookup::$__ALLOW)	{
					$accept = true;
				} else {
					//Hata mimi hapa sielewi , lakini kazi iendelee
					$accept = false;
				}
			} else if ($jobContextChar1 == ContextLookup::$__DENY)	{
				$accept = false;
			} else if ($jobContextChar1 == ContextLookup::$__ALLOW)	{
				$accept = true;
			} else {
				//Hapaeleweki hapa, ila basi kazi iendelee
				$accept = false;
			}
		} else if ($loginContextChar1 == ContextLookup::$__DENY)	{
			$accept = false;
		} else if ($loginContextChar1 == ContextLookup::$__ALLOW)	{
			$accept = true;
		} else {
			//Hapasomeki,
			$accept = false;
		}
		if ((! $accept) && ($setlog=="setlog")) self::setSession($opname);
		return $accept;
	}	
	public final static function isAllowable($config1 /* ::ConfigurationData */, $opname /*manageprofile_edit*/, $optype = "normal", $setlog = "donotsetlog", $targetLoginId = null, $targetGroupId = null)	{
		$host = $config1->getHostname();
        $dbname = $config1->getDatabase();
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
		$accept = self::isAllowable2($conn, $opname, $optype, $setlog, $targetLoginId, $targetGroupId);
		$conn = null;
		return $accept;
	}
	public final static function isCompositeAllowable2($conn /* ::ConfigurationData */, $opList /*[manageprofile_edit]*/, $optype = "normal", $setlog = "donotsetlog", $targetLoginId = null, $targetGroupId = null, $defaultLogic = "AND")	{
		//It is Either AND or Anything (Anything is Considered as OR)
		$isAnded = strtolower($defaultLogic) === "and" ? true : false;
		$accept = $isAnded;
		foreach ($opList as $opname)	{
			$accept = self::isAllowable2($conn, $opname, $optype, $setlog, $targetLoginId, $targetGroupId);
			if ($isAnded xor $accept) break;
		}
		return $accept;
	}	
	public final static function isCompositeAllowable($config1 /* ::ConfigurationData */, $opList /*[manageprofile_edit]*/, $optype = "normal", $setlog = "donotsetlog", $targetLoginId = null, $targetGroupId = null, $defaultLogic = "AND")	{
		$host = $config1->getHostname();
        $dbname = $config1->getDatabase();
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $config1->getUsername(), $config1->getPassword());
		$accept = self::isCompositeAllowable2($conn, $opList, $optype, $setlog, $targetLoginId, $targetGroupId, $defaultLogic);
		$conn = null;
		return $accept;
	}
}
?>
