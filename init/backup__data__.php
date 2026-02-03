<?php
/*
Developed by Ndimangwa Fadhili Ngoya
Developed on 15th April, 2021
Phone: +255 787 101 808 / +255 762 357 596
Email: ndimangwa@gmail.com 

Initialization codes for data related objects
*/
/*
Pending Work on
SwitchSelect Control to include list-item:count 
function createFormSwitchSelect ...
*/
abstract class __data__ extends __object__
{
	public static $__TRUE = 1;
	public static $__FALSE = 0;
	public static $__PROFILE_INIT_ID = 1;
	public static $__LOGIN_INIT_ID = 1;
	public static $__USER_INIT_ID = 1;
	public static $__LIST_EMPTY_MESSAGE = "The List Item is Empty";
	public static $__INDEX_PLACEHOLDER = ":__index__:";
	private static $__DEFAULT_GRID_NAME = "__my_default_grid_name_d_7888_ivh__";
	private $l__update = array(); //used to keep track of updates
	private $p_l_update = array(); //Keep property updates
	private static $__CHECKBOX_NAME = "__bin_ndimangwa_checkbox__";
	public static $__ALL32BITS_SET = 4294967295;
	//RecordCount
	public static function getRecordCountCreated($tablename, $fromTime1, $toTime1, $currentTime1 = null)	{

	}
	public static function getRecordCountUpdated($tablename, $fromTime1, $toTime1, $currentTime1 = null)	{
		
	}
	//Replace caption with placeholder
	public static function getListOfColumns($classname, $format, & $listOfColumnPlaceholder)  {
		$listOfColumns = array(); //pname
		$pname = "";
		$placeholder = "";
		$tpname = "";
		$state = 0;
		foreach (str_split($format) as $char)   {
			if ($char == '.' && $state == 0)    {
				$placeholder = $char;
				$pname = ""; $tpname = "";
				$state = 1;
			} else if ($char == '{' && $state == 1) {
				$placeholder .= $char;
				$state = 2;
			} else if ($state == 1) {
				$state = 0;
			} else if ($char == '}' && $state == 2) {
				$placeholder .= $char;
				$tpname .= $char;
				$state = 3;
			} else if ($char == '.' && $state == 3) {
				$placeholder .= $char;
				//Now perform extraction
				$pname = trim($pname);
				$col = Registry::property2column($classname, $pname);
				if (! is_null($col))    {
					$listOfColumns[sizeof($listOfColumns)] = $col;
					$listOfColumnPlaceholder[$col] = $placeholder;
				}
				$state = 0;
			} else if ($state == 3) {
				$placeholder .= $char;
				$pname .= $tpname;
				$tpname = "";
				$state = 2;
			} else if ($state == 2) {
				$pname .= $char;
				$placeholder .= $char;
			}
		}
		return $listOfColumns;
	}	
	private static function name2id($name)
	{
		return str_replace("]", "_", str_replace("[", "_", $name));
	}
	public function toString()
	{
		return "Data Set";
	}
	public static function convertListObjectsToCommaSeparatedValues($listObjects = null)
	{
		if (is_null($listObjects)) return "";
		$list = null;
		foreach ($listObjects as $object1) {
			$dt = $object1->getId0();
			if (is_null($list)) $list = "$dt";
			else $list .= ",$dt";
		}
		return is_null($list) ? "" : $list;
	}
	public static function filterPayload($payload, $includeList = null, $excludeList = null, $filterNulls = false)
	{
		//$payload is our 1D payload data
		//includeList and excludeList are both arrays
		$list = array();
		foreach ($payload as $pname => $value) {
			if (
				(is_null($includeList) || in_array($pname, $includeList)) &&
				(is_null($excludeList) || !in_array($pname, $excludeList)) &&
				(!$filterNulls || !is_null($value))
			) {
				$list[$pname] = $value;
			}
		}
		return $list;
	}
	//ReferenceString 
	public function isMemberOfObjectReference($referenceString)
	{
		if (is_null($referenceString)) return false;
		$classname = $this->getMyClassname();
		if (!in_array($classname, array('Login', 'JobTitle', 'Group'))) return false;
		if ($classname == "Login") {
			return ($this->getObjectReferenceString() == $referenceString || $this->getJobTitle()->isMemberOfObjectReference($referenceString) || $this->getGroup()->isMemberOfObjectReference($referenceString));
		} else if ($classname == "JobTitle") {
			return ($this->getObjectReferenceString() == $referenceString);
		} else if ($classname == "Group") {
			$pgroup1 = $this->getParentGroup();
			return (($this->getObjectReferenceString() == $referenceString) || (!is_null($pgroup1) && $pgroup1->isMemberOfObjectReference($referenceString)));
		}
		return false;
	}
	//Working with Data
	public static function convertRawSQLDataToTabularData($conn, $classname, $rows, $usemap = null)
	{
		$tArray1 = array();
		$primaryColumn = Registry::getId0Columnname($classname);
		foreach ($rows as $row) {
			$index = sizeof($tArray1);
			$tArray1[$index] = array();
			foreach ($row as $colname => $value) {
				$pname = Registry::column2Property($classname, $colname);
				if (is_null($pname)) continue;
				if ($colname == $primaryColumn) $pname = "id";
				//We need to work with Dates 
				$refclass = Registry::getReferenceClass($classname, $pname);
				if ($refclass == "DateAndTime") {
					try {
						//$value = ~DateAndTime~::~convertFromSystemDateAndTimeFormatgetGUIDateOnlyFormat($value);
						$dt1 = new DateAndTime($value);
						$value = $dt1->getGUIDateOnlyFormat();
					} catch (Exception $e) {
					}
				}
				//We need to work for value 
				$t1 = Registry::columnTransitiveMap($classname, $pname);
				$tmap = is_null($usemap) ? $t1 : (isset($usemap[$pname]) ? $usemap[$pname] : $t1);
				$newValue = null;
				if (!is_null($tmap) && is_array($tmap)) {
					foreach ($tmap as $tclassproperty) {
						$tval = self::getValueOfAClassProperty($conn, $tclassproperty, $value);
						if (!is_null($tval)) {
							if (is_null($newValue)) $newValue = $tval;
						} else {
							$newValue .= " -- $tval";
						}
					}
				}
				if (is_null($newValue)) $newValue = $value;
				$tArray1[$index][$pname] = $newValue;
			}
		}
		return $tArray1;
	}
	public static function getValueOfAClassProperty($conn, $classproperty /* ie Sex.sexName */, $pid /*  ie 2*/)
	{
		// return value of a property of a class given a primary value 
		if (is_null($classproperty)) return null;
		$tArray1 = explode(".", $classproperty);
		if (sizeof($tArray1) != 2) return null;
		$classname = $tArray1[0];
		$pname = $tArray1[1];
		$primaryColumn = Registry::getId0Columnname($classname);
		$colname = Registry::property2column($classname, $pname);
		if (is_null($primaryColumn)) return null;
		$jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
			array(Registry::getTablename($classname)),
			array($colname),
			array($primaryColumn => $pid)
		), $conn);
		$jArray1 = json_decode($jresult1, true);
		if (is_null($jArray1)) return null;
		if ($jArray1['code'] !== 0) return null;
		if ($jArray1['count'] !== 1) return null; //Must be one , since we submited pid -- unique 
		return $jArray1['rows'][0][$colname];
	}
	//Alerts And Warnings 
	private static function getAlert($message, $alertType)
	{
		return "<div class=\"alert $alertType\" role=\"alert\">$message</div>";
	}
	public static function showPrimaryAlert($message)
	{
		return self::getAlert($message, "alert-primary");
	}
	public static function showDangerAlert($message)
	{
		return self::getAlert($message, "alert-danger");
	}
	public static function showWarningAlert($message)
	{
		return self::getAlert($message, "alert-warning");
	}
	//FormControls 
	public static function createDetailsPage($page, $classname, $payload /* 1D columnlist */, $conn, $id, $extraControls = null, $appendData = array())
	{
		if (is_null($appendData)) $appendData = array(); /*colname => data*/
		$line = "<div class=\"data-details\"><table class=\"table\"><thead class=\"thead-dark\"><th scope=\"col\"></th><th>Name</th><th>Value</th></thead><tbody>";
		$tablename = Registry::getTablename($classname);
		$primaryColumn = Registry::getId0Columnname($classname);
		$whereArray1 = array();
		$whereArray1[$primaryColumn] = $id;
		$listOfColumns = array();
		$typeOfColumns = array();
		foreach ($payload as $pname) {
			$colname = Registry::property2Column($classname, $pname);
			if (!is_null($colname)) {
				$listOfColumns[sizeof($listOfColumns)] = $colname;
				$coltype = Registry::getColumnType($classname, $pname);
				$typeOfColumns[$colname] = is_null($coltype) ? "text" : $coltype;
			}
		}
		$jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
			array($tablename),
			$listOfColumns,
			$whereArray1
		), $conn);
		if (is_null($jresult1)) throw new Exception("Results Returned null");
		$jArray1 = json_decode($jresult1, true);
		if (is_null($jArray1)) throw new Exception("Malformed Return Value");
		if ($jArray1['code'] != 0) throw new Exception($jArray1['message']);
		if ($jArray1['count'] != 1) throw new Exception("Empty, or Duplicate records");
		$rows = self::convertRawSQLDataToTabularData($conn, $classname, $jArray1['rows']);
		$row = $rows[0];
		$count = 0;
		foreach ($listOfColumns as $column) {
			$pname = Registry::column2property($classname, $column);
			if (!is_null($pname)) {
				$sn = $count + 1;
				$caption = __object__::property2Caption($pname);
				$value = $row[$pname];
				if (is_null($value)) continue;
				if ($value == "") continue;
				if ($typeOfColumns[$column] == "list-object")	{
					$refClass = Registry::getReferenceClass($classname, $pname);
					$tvalue = "<div class=\"td-list-object\" style=\"font-size: 0.9em;\">";
					if (! is_null($refClass))	{
						$tcount = 0;
						foreach (explode(",", $value) as $tid)	{
							try {
								$tobject1 = Registry::getObjectReference("Hello", $conn, $refClass, $tid);
								$tval = $tobject1->getName0();
								if (! is_null($tval))	{
									$tindex = $tcount + 1;
									$tval = "<span style=\"font-style: italic;\">($tindex) : $tval</span>";
									$tvalue .= ($tcount == 0) ? $tval : ( "<br/>" . $tval );
								}
							} catch (Exception $e)	{
								$tcount--;
							}
							$tcount++;
						}
					}
					$tvalue .= "</div>";
					$value = $tvalue;
				} else if ($typeOfColumns[$column] == "boolean")	{
					$value = (intval($value) == 1) ? "True or Yes" : "False or No";
				}
				//We need to do translation
				if (isset($appendData[$pname])) $value .= $appendData[$pname];
				$line .= "<tr><th scope=\"row\">$sn</th><td>$caption</td><td>$value</td></tr>";
				$count++;
			}
		}
		$line .= "</tbody></table>";
		//Working with further controls 
		if (!is_null($extraControls)) {
			$line .= "<div class=\"extra-controls-container\" class=\"mt-2 mr-2 mb-2 ml-auto text-right\">";
			$count = 0;
			foreach ($extraControls as $controlBlock1) {
				if (!(isset($controlBlock1['pname']) && isset($controlBlock1['href']))) continue;
				$pname = $controlBlock1['pname'];
				$href = $controlBlock1['href'];
				$caption = null;
				if (isset($controlBlock1['caption'])) $caption = $controlBlock1['caption'];
				$policy = null;
				if (isset($controlBlock1['policy'])) $policy = $controlBlock1['policy'];
				$title = null;
				if (isset($controlBlock1['title']))	$title = $controlBlock1['title'];
				$icon = null;
				if (isset($controlBlock1['icon-class'])) $icon = $controlBlock1['icon-class'];
				if (is_null($policy) || $policy) {
					$m_left = "ml-2";
					if ($count == 0) $m_left = "";
					if (!is_null($title)) $title = "title = \"$title\"";
					else $title = "";
					if (is_null($caption)) $caption = "";
					if (!is_null($icon)) $icon = "<i class=\"$icon\"></i>";
					else $icon = "";
					$line .= "<a class=\"cmd cmd_$pname $m_left\" href=\"$href\" data-id=\"$id\" data-class=\"$classname\" data-toggle=\"tooltip\" $title>$icon $caption</a>";
					$count++;
				}
			}
			$line .= "</div>";
		}
		$line .= "</div>";
		return $line;
	}
	public static function createConfirmationForm($page, $classname, $message, $buttonText = "Confirm", $mode = "delete", $conn = null, $id = -1, $customHiddenFields = null, $customContextName = null)
	{
		$formid = __object__::getCodeString(16);
		$errorid = "__form_error_$formid";
		$formid = "__form_reference_$formid";
		$line = "<div><form class=\"form-horizontal\" id=\"$formid\" method=\"POST\">";
		$line .= "<input type=\"hidden\" name=\"__classname__\" value=\"$classname\"/>";
		if (!is_null($customContextName)) $line .= "<input type=\"hidden\" name=\"__custom_context_name__\" value=\"$customContextName\"/>";
		$line .= "<input type=\"hidden\" name=\"__query__\" value=\"$mode\"/>";
		if (!is_null($customHiddenFields)) {
			foreach ($customHiddenFields as $keyname => $val) {
				$line .= "<input type=\"hidden\" name=\"$keyname\" value=\"$val\"/>";
			}
		}
		$line .= "<div><label>$message</label></div><div id=\"$errorid\" class=\"p-2 ui-sys-error-message\"></div>";
		$line .= "<div><button class=\"btn btn-danger btn-block btn-click-default btn-execute-on-click btn-send-dialog-ajax\" type=\"button\" data-form-submit=\"$formid\" data-next-page=\"$page\" data-form-error=\"$errorid\">$buttonText</button></div>";
		$line .= "</form></div>";
		return $line;
	}
	public static function createMutualExclusiveControls($conn, $classname, $caption, $fieldBlock1, $fieldBlock2, $checked = true, $update = false, $class_id = null)
	{
		$object1 = null;
		if ($update) $object1 = Registry::getObjectReference("Delta", $conn, $classname, $class_id);
		$compulsoryFields = array('pname');
		foreach ($compulsoryFields as $field) if (!isset($fieldBlock1[$field]) && !isset($fieldBlock2[$field])) throw new Exception("[ $field ] : Not set in either block1 or block2");
		//pname 
		$pname1 = $fieldBlock1['pname'];
		$pname2 = $fieldBlock2['pname'];
		//caption
		$caption1 = null;
		if (isset($fieldBlock1['caption'])) $caption1 = $fieldBlock1['caption'];
		else $caption1 = __object__::property2Caption($pname1);
		$caption2 = null;
		if (isset($fieldBlock2['caption'])) $caption2 = $fieldBlock2['caption'];
		else $caption2 = __object__::property2Caption($pname2);
		//required
		$required1 = true;
		if (isset($fieldBlock1['required'])) $required1 = $fieldBlock1['required'];
		$required2 = true;
		if (isset($fieldBlock2['required'])) $required2 = $fieldBlock2['required'];
		//placeholder 
		$placeholder1 = null;
		if (isset($fieldBlock1['placeholder'])) $placeholder1 = $fieldBlock1['placeholder'];
		$placeholder2 = null;
		if (isset($fieldBlock2['placeholder'])) $placeholder2 = $fieldBlock2['placeholder'];
		//validationColumnName
		$validationColumnName1 = null;
		if (isset($fieldBlock1['validation-column-name'])) $validationColumnName1 = $fieldBlock1['validation-column-name'];
		$validationColumnName2 = null;
		if (isset($fieldBlock2['validation-column-name'])) $validationColumnName2 = $fieldBlock2['validation-column-name'];
		//uiControl
		$uiRenderControl1 = null;
		if (isset($fieldBlock1['ui-control'])) $uiRenderControl1 = $fieldBlock1['ui-control'];
		$uiRenderControl2 = null;
		if (isset($fieldBlock2['ui-control'])) $uiRenderControl2 = $fieldBlock2['ui-control'];
		//We need to work with type 
		$type1 = Registry::getColumnType($classname, $pname1);
		$type2 = Registry::getColumnType($classname, $pname2);
		if (isset($fieldBlock1['type'])) $type1 = $fieldBlock1['type'];
		if (isset($fieldBlock2['type'])) $type2 = $fieldBlock2['type'];
		if (is_null($type1) || is_null($type2)) throw new Exception("One of the types, could not be figured");
		//propertyValue 
		$propertyValue1 = null;
		$propertyValue2 = null;
		if (!is_null($object1)) {
			$propertyValue1 = $object1->getMyPropertyValue($pname1);
			$propertyValue2 = $object1->getMyPropertyValue($pname2);
		}
		if (isset($fieldBlock1['value'])) 	$propertyValue1 = $fieldBlock1['value'];
		if (isset($fieldBlock2['value'])) 	$propertyValue2 = $fieldBlock2['value'];
		//Checked Status 
		$checkedstatus = "";
		if ($checked) {
			$checkedstatus = "checked";
			$fieldBlock2['disabled'] = true;
		} else {
			$fieldBlock1['disabled'] = true;
		}
		$line = "";
		//1st Control
		if ($type1 == "object") {
			$line .= self::createFormSelectInput($conn, $classname, $pname1, $caption1, $propertyValue1, $required1, $validationColumnName1, $fieldBlock1);
		} else if ($type1 == "text") {
			$line .= self::createFormTextInput($classname, $pname1, $caption1, $propertyValue1, $required1, $validationColumnName1, $placeholder1, $fieldBlock1);
		} else if ($type1 == "textarea") {
			$line .= self::createFormTextAreaInput($classname, $pname1, $caption1, $propertyValue1, $required1, $validationColumnName1, $placeholder1, $fieldBlock1);
		} else if ($type1 == "ckeditor") {
			$fieldBlock1['ckeditor'] = true;
			$line .= self::createFormTextAreaInput($classname, $pname1, $caption1, $propertyValue1, $required1, $validationColumnName1, $placeholder1, $fieldBlock1);
		} else if ($type1 == "file") {
			$line .= self::createFormFileInput($classname, $pname1, $caption1, $propertyValue1, $required1, $validationColumnName1, $placeholder1, $fieldBlock1);
		} else if ($type1 == "list-object") {
			$line .= self::createFormListSelection($conn, $classname, $pname1, $propertyValue1, $required1, $validationColumnName1, $fieldBlock1);
		} else if ($type1 == "integer") {
			$line .= self::createFormNumberInput($classname, $pname1, $caption1, $propertyValue1, $required1, $validationColumnName1, $placeholder1, $fieldBlock1);
		} else if ($type1 == "float") {
			$line .= self::createFormNumberInput($classname, $pname1, $caption1, $propertyValue1, $required1, $validationColumnName1, $placeholder1, $fieldBlock1);
		} else if ($type1 == "email") {
			$line .= self::createFormEmailInput($classname, $pname1, $caption1, $propertyValue1, $required1, $validationColumnName1, $placeholder1, $fieldBlock1);
		} else if ($type1 == "date") {
			$line .= self::createFormDateInput($classname, $pname1, $caption1, $propertyValue1, $required1, $validationColumnName1, $placeholder1, $fieldBlock1);
		} else if ($type1 == "switch-text") {
			$line .= self::createFormSwitchTextInput($classname, $pname1, $caption1, $propertyValue1, $required1, $validationColumnName1, $placeholder1, $fieldBlock1);
		} else if ($type1 == "switch-select") {
			$line .= self::createFormSwitchSelectInput($conn, $classname, $pname1, $caption1, $propertyValue1, $required1, $validationColumnName1, $fieldBlock1);
		}
		//2nd Control
		if ($type2 == "object") {
			$line .= self::createFormSelectInput($conn, $classname, $pname2, $caption2, $propertyValue2, $required2, $validationColumnName2, $fieldBlock2);
		} else if ($type2 == "text") {
			$line .= self::createFormTextInput($classname, $pname2, $caption2, $propertyValue2, $required2, $validationColumnName2, $placeholder2, $fieldBlock2);
		} else if ($type2 == "textarea") {
			$line .= self::createFormTextAreaInput($classname, $pname2, $caption2, $propertyValue2, $required2, $validationColumnName2, $placeholder2, $fieldBlock2);
		} else if ($type2 == "ckeditor") {
			$fieldBlock2['ckeditor'] = true;
			$line .= self::createFormTextAreaInput($classname, $pname2, $caption2, $propertyValue2, $required2, $validationColumnName2, $placeholder2, $fieldBlock2);
		} else if ($type2 == "file") {
			$line .= self::createFormFileInput($classname, $pname2, $caption2, $propertyValue2, $required2, $validationColumnName2, $placeholder2, $fieldBlock2);
		} else if ($type2 == "list-object") {
			$line .= self::createFormListSelection($conn, $classname, $pname2, $propertyValue2, $required2, $validationColumnName2, $fieldBlock2);
		} else if ($type2 == "integer") {
			$line .= self::createFormNumberInput($classname, $pname2, $caption2, $propertyValue2, $required2, $validationColumnName2, $placeholder2, $fieldBlock2);
		} else if ($type2 == "float") {
			$line .= self::createFormNumberInput($classname, $pname2, $caption2, $propertyValue2, $required2, $validationColumnName2, $placeholder2, $fieldBlock2);
		} else if ($type2 == "email") {
			$line .= self::createFormEmailInput($classname, $pname2, $caption2, $propertyValue2, $required2, $validationColumnName2, $placeholder2, $fieldBlock2);
		} else if ($type2 == "date") {
			$line .= self::createFormDateInput($classname, $pname2, $caption2, $propertyValue2, $required2, $validationColumnName2, $placeholder2, $fieldBlock2);
		} else if ($type2 == "switch-text") {
			$line .= self::createFormSwitchTextInput($classname, $pname2, $caption2, $propertyValue2, $required2, $validationColumnName2, $placeholder2, $fieldBlock2);
		} else if ($type2 == "switch-select") {
			$line .= self::createFormSwitchSelectInput($conn, $classname, $pname2, $caption2, $propertyValue2, $required2, $validationColumnName2, $fieldBlock2);
		}
		$window1 = $line;
		//Building a switch ui
		$switchId = __object__::getCodeString(16);
		$line = "<div class=\"form-group row\"><div class=\"col-sm-2\"><input id=\"$switchId\" type=\"checkbox\" $checkedstatus data-bootstrap-switch data-off-color=\"danger\" data-on-color=\"success\"/></div><label class=\"col-sm-10 col-form-label\">$caption</label></div>";
		$window1 = $line . $window1;
		$window1 .= "<script type=\"text/javascript\">    function updateUI(checked)  {        var \$control1 = $(\"#$pname1\");        var \$control2 = $(\"#$pname2\");        if (! (\$control1.length && \$control2.length)) return false;        \$control1.prop('disabled', ! checked);        \$control2.prop('disabled', checked);    }    $('#$switchId').on('change.bootstrapSwitch', function(e)    {        updateUI(e.target.checked);    })</script>";
		return "<div class=\"border border-outline-primary my-2\">$window1</div>";
	}
	public function getMyDetailView($payload /*2D Array*/, $valueShapingFunction = null)
	{
		$line = "<div class=\"data-details\"><table class=\"table\"><thead class=\"thead-dark\"><tr><th scope=\"col\"></th><th scope=\"col\">Name</th><th scope=\"col\">Value</th></tr></thead><tbody>";
		$count = 0;
		$dcount = 0;
		foreach ($payload as $fieldBlock1) {
			$classname = $this->getMyClassname();
			$object1 = $this;
			if (!isset($fieldBlock1['pname'])) continue;
			$pname = $fieldBlock1['pname'];
			try {
				if (isset($fieldBlock1['use-class'])) {
					$classname = $fieldBlock1['use-class'];
					if (!is_null($object1)) {
						//We need to acquire a right property
						$oproperty = Registry::getListOfPropertiesOfClass($this->getMyClassname(), $classname);
						if (is_null($oproperty)) throw new Exception("Could not get reference object properties");
						$object1 = $object1->getMyPropertyValue($oproperty[0]);
					}
				}
			} catch (Exception $e) {
				$classname = $this->getMyClassname();
				$object1 = $this;
			}
			$type = Registry::getColumnType($classname, $pname);
			if (is_null($type)) $type = "text";
			$caption = __object__::property2Caption($pname);
			if (isset($fieldBlock1['caption'])) $caption = $fieldBlock1['caption'];
			$value = $object1->getMyPropertyValue($pname);
			if (!is_null($value)) {
				switch ($type) {
					case "object":
						$value = $value->getName0();
						break;
				}
				$sn = $dcount + 1;
				if (!is_null($valueShapingFunction) && is_callable($valueShapingFunction)) $value = $valueShapingFunction($pname, $value);
				$line .= "<tr><th scope=\"row\">$sn</th><td>$caption</td><td>$value</td></tr>";
				$dcount++;
			}
			$count++;
		}
		$line .= "</tbody></table></div>";
		return $line;
	}
	private static function createCustomTabularForm($conn, $classname, $payload, $classid = null)	{
		$classid = in_array($classid, array(-1, 0)) ? null : $classid;
		$enableSerialNumber = true;
		$serialNumberStartAt = 1;
		$serialNumberStep = 1;
		$rowIdsHiddenControls = "";
		if (isset($payload['settings']))	{
			$settingsBlock1 = $payload['settings'];
			//Working with serial-number
			if (isset($settingsBlock1['serial-number']))	{
				$serialNumberBlock1 = $settingsBlock1['serial-number'];
				$enableSerialNumber = isset($serialNumberBlock1['enable']) ? $serialNumberBlock1['enable'] : $enableSerialNumber;
				$serialNumberStartAt = isset($serialNumberBlock1['start-at']) ? $serialNumberBlock1['start-at'] : $serialNumberStartAt;
				$serialNumberStep = isset($serialNumberBlock1['step']) ? $serialNumberBlock1['step'] : $serialNumberStep;
			}
			//Working with ids 
			if (isset($settingsBlock1['row-id']) && isset($payload['row-ids']) )	{
				$rowIdBlock1 = $settingsBlock1['row-id'];
				if (isset($rowIdBlock1['name']))	{
					$name = $rowIdBlock1['name'];
					foreach ($payload['row-ids'] as $key => $value)	{
						$tname = $name . "[" . $key . "]";
						$rowIdsHiddenControls .= "<input type=\"hidden\" name=\"$tname\" value=\"$value\"/>";
					}
				}
			}
		}
		//Now checking other important fields
		$window1 = "<div class=\"table-general-container\">$rowIdsHiddenControls<div class=\"table-responsive\"><table class=\"table\"><thead><tr>";
		//Working with Header
		$snText = $enableSerialNumber ? "<th scope=\"col\">S/N</th>" : "";
		$window1 .= $snText;
		if (! isset($payload['colnames'])) return "";
		$listOfColumns = array();
		$classMap = array();
		$classIdMap = array();
		$typeMap = array();
		$requiredMap = array();
		$placeholderMap = array();
		$readonlyMap = array();
		foreach ($payload['colnames'] as $colblock1)	{
			if (! isset($colblock1['pname'])) continue;
			$pname = $colblock1['pname'];
			$caption = isset($colblock1['caption']) ? $colblock1['caption'] : ( __object__::property2Caption($pname) );
			$listOfColumns[sizeof($listOfColumns)] = $pname;
			$classMap[$pname] = isset($colblock1['use-class']) ? $colblock1['use-class'] : $classname;
			$classIdMap[$pname] = isset($colblock1['class-id']) ? $colblock1['class-id'] : $classid;
			$typeMap[$pname] = isset($colblock1['type']) ? $colblock1['type'] : ( Registry::getColumnType($classMap[$pname], $pname) );
			$requiredMap[$pname] = isset($colblock1['required']) ? $colblock1['required'] : true;
			$placeholderMap[$pname] = isset($colblock1['placeholder']) ? $colblock1['placeholder'] : "";
			$readonlyMap[$pname] = isset($colblock1['readonly']) ? $colblock1['readonly'] : false;
			$window1 .= "<th scope=\"col\">$caption</th>";
		}
		//End with Header
		$window1 .= "</tr></thead><tbody>";
		//Working with Body -- Start
		if (! isset($payload['rows'])) return "";
		$rowcount = 0;
		$sn = $serialNumberStartAt;
		foreach ($payload['rows'] as $row1)	{
			$snText = $enableSerialNumber ? "<th scope=\"row\">$sn</th>" : "";
			$window1 .= "<tr>$snText";
			foreach ($listOfColumns as $i => $pname) {
				$cell1 = isset($row1[$i]) ? $row1[$i] : null;
				if (is_null($cell1))	{
					$window1 .= "<td></td>";
				} else {
					//This is where we need to work
					//Now override if any
					$required = isset($cell1['required']) ? $cell1['required'] : $requiredMap[$pname];
					$placeholder = isset($cell1['placeholder']) ? $cell1['placeholder'] : $placeholderMap[$pname];
					$readonly = isset($cell1['readonly']) ? $cell1['readonly'] : $readonlyMap[$pname];
					$tclassname = $classMap[$pname];
					$tclassid = $classIdMap[$pname];
					$object1 = is_null($tclassid) ? null : ( Registry::getObjectReference("Delta", $conn, $tclassname, $tclassid) );
					$type = $typeMap[$pname];
					if (is_null($type))	{
						$window1 .= "<td></td>";
					} else {
						$t1 = null;
						$fieldBlock1 = array(
							"control-only" => true,
							"readonly" => $readonly
						);
						$name = $pname."[". $rowcount ."]";
						//Working with value 
						$value = isset($cell1['value']) ? $cell1['value'] : ( is_null($object1) ? null : ( $object1->getMyPropertyValue($pname) ) );
						switch ($type)	{
							case "object":
								$t1 = self::createFormSelectInput($conn, $tclassname, $name, "Init", $value, $requiredMap[$pname], null, $fieldBlock1);
								break;
							case "text":
								$t1 = self::createFormTextInput($tclassname, $name, "Init", $value, $requiredMap[$pname], null, $placeholderMap[$pname], $fieldBlock1);
								break;
							case "textarea":
								$t1 = self::createFormTextAreaInput($tclassname, $name, "Init", $value, $requiredMap[$pname], null, $placeholderMap[$pname], $fieldBlock1);
								break;
							case "ckeditor":
								$fieldBlock1['ckeditor'] = true;
								$t1 = self::createFormTextAreaInput($tclassname, $name, "Init", $value, $requiredMap[$pname], null, $placeholderMap[$pname], $fieldBlock1);
								break;
							case "file":
								$t1 = self::createFormFileInput($tclassname, $name, "Init", $value, $requiredMap[$pname], null, $placeholderMap[$pname], $fieldBlock1);
								break;
							case "list-object":
								$t1 = self::createFormListSelection($conn, $tclassname, $name, $value, $requiredMap[$pname], null, $fieldBlock1);
								break;
							case "integer":
								$t1 = self::createFormNumberInput($tclassname, $name, "Init", $value, $requiredMap[$pname], null, $placeholderMap[$pname], $fieldBlock1);
								break;
							case "float":
								$t1 = self::createFormNumberInput($tclassname, $name, "Init", $value, $requiredMap[$pname], null, $placeholderMap[$pname], $fieldBlock1);
								break;
							case "boolean":
								$t1 = self::createFormCheckboxInput($tclassname, $name, "Init", false, $requiredMap[$pname], $fieldBlock1);
								break;
							case "email":
								$t1 = self::createFormEmailInput($tclassname, $name, "Init", $value, $requiredMap[$pname], null, $placeholderMap[$pname], $fieldBlock1);
								break;
							case "date":
								$t1 = self::createFormDateInput($tclassname, $name, "Init", $value, $requiredMap[$pname], null, $placeholderMap[$pname], $fieldBlock1);
								break;
							case "switch-text":
								$t1 = self::createFormSwitchTextInput($tclassname, $name, "Init", $value, $requiredMap[$pname], null, $placeholderMap[$pname], $fieldBlock1);
								break;
							case "switch-select":
								$t1 = self::createFormSwitchSelectInput($conn, $tclassname, $name, "Init", $value, $requiredMap[$pname], null, $fieldBlock1);
								break;
							default:
								$t1 = null;
						}
						$window1 .= ( is_null($t1) ? "<td></td>" : ("<td>" . $t1 . "</td>") );
					}
				}
			}
			$window1 .= "</tr>";
			$sn += $serialNumberStep;
			$rowcount++;
		}
		//End Working with Body
		$window1 .= "</tbody></table></div></div>";
		return $window1;
	}
	public static function createDataCaptureForm($page, $hostclassname, $payload /*2D array*/, $buttonText = "Submit Data", $mode = "create", $conn = null, $id = -1, $customHiddenFields = null /*key value*/, $customContextName = null, $serverScript = null, $customDataSubmissionClass = "btn-send-dialog-ajax", $formAction = null, $useNormalSubmitButton = false, $customJSONPayload = null)
	{
		$formid = __object__::getCodeString(16);
		$errorid = "__form_error_$formid";
		$buttonid = "__button_reference_$formid";
		$formid = "__form_reference_$formid";
		$action = "";
		if (!is_null($formAction)) $action = "action = \"$formAction\"";
		if (is_null($customDataSubmissionClass)) $customDataSubmissionClass = "";
		//find reference on pname
		$formEnctype = "";
		//Check if contains file type and work for data-grid
		$listOfFieldsWithGrid = array();
		$listOfGridsByName = array();
		$gridRowCountByName = array();
		$serialNumberingByName = array();
		$previousRowCountByName = array();
		$previousSerialNumberingByName = array();
		if (in_array($mode, array("custom-tabular")) && ! is_null($customJSONPayload)) {
			$fieldBlock1 = array(); //Wipe out to undo the comming loops
			$listOfGridsByName = array();
		}
		foreach ($payload as $key => $fieldBlock1) {
			//We need to have all index for all columns in each row
			foreach ($payload as $tkey => $fieldBlock2) {
				if (isset($fieldBlock2['pname'])) {
					$pname = $fieldBlock2['pname'];
					$payload[$key]['__pname_index__'][$pname] = $tkey;
				}
			}
			$payload[$key]['__payload__'] = $payload;
			if (isset($fieldBlock1['type']) && $fieldBlock1['type'] == "file") {
				$formEnctype = "enctype = \"multipart/form-data\"";
			}
			//Working for data-grid
			if (isset($fieldBlock1['pname']) && isset($fieldBlock1['type']) && $fieldBlock1['type'] == "data-grid")	{
				$pname = $fieldBlock1['pname'];
				$rowCount = (isset($fieldBlock1['grid-settings']) && isset($fieldBlock1['grid-settings']['row-count'])) ? intval($fieldBlock1['grid-settings']['row-count']) : 1;
				$gridName = (isset($fieldBlock1['grid-settings']) && isset($fieldBlock1['grid-settings']['grid-name'])) ? $fieldBlock1['grid-settings']['grid-name'] : ( self::$__DEFAULT_GRID_NAME );
				$serialNumbering = (isset($fieldBlock1['grid-settings']) && isset($fieldBlock1['grid-settings']['serial-numbering'])) ? $fieldBlock1['grid-settings']['serial-numbering'] : true;
				//Now Adjusting Based on previous settings 
				if (! isset($previousRowCountByName[$gridName])) $previousRowCountByName[$gridName] = 1;
				if (! isset($previousSerialNumberingByName[$gridName])) $previousSerialNumberingByName[$gridName] = true;
				//Now adjust
				$rowCount = max($previousRowCountByName[$gridName], $rowCount);
				$serialNumbering = ( $previousSerialNumberingByName[$gridName] && $serialNumbering ); 
				//Update previous settings
				$previousRowCountByName[$gridName] = $rowCount;
				$previousSerialNumberingByName[$gridName] = $serialNumbering;
				//Now do saving 
				$listOfFieldsWithGrid[sizeof($listOfFieldsWithGrid)] = $pname;
				if (! isset($listOfGridsByName[$gridName])) $listOfGridsByName[$gridName] = array();
				$listOfGridsByName[$gridName][sizeof($listOfGridsByName[$gridName])] = $pname;
				$gridRowCountByName[$gridName] = $rowCount;
				$serialNumberingByName[$gridName] = $serialNumbering;
			}
		}
		$line = "<div><form class=\"form-horizontal\" method=\"POST\" $action id=\"$formid\" $formEnctype>";
		//Hidden Fields
		$line .= "<input type=\"hidden\" name=\"__classname__\" value=\"$hostclassname\"/>";
		if (!is_null($customContextName)) $line .= "<input type=\"hidden\" name=\"__custom_context_name__\" value=\"$customContextName\"/>";
		$line .= "<input type=\"hidden\" name=\"__query__\" value=\"$mode\"/>";
		if (!is_null($customHiddenFields)) {
			foreach ($customHiddenFields as $keyname => $val) {
				$line .= "<input type=\"hidden\" name=\"$keyname\" value=\"$val\"/>";
			}
		}
		//Ui Control Fields
		$hostobject1 = null;
		if ($mode == "update") $hostobject1 = Registry::getObjectReference("Ndimangwa-Ngoya", $conn, $hostclassname, $id);
		foreach ($payload as $fieldBlock1) {
			$classname = $hostclassname;
			$object1 = $hostobject1;
			if (!isset($fieldBlock1['pname'])) continue;
			$pname = $fieldBlock1['pname'];
			//Skip if pname in grid
			if (in_array($pname, $listOfFieldsWithGrid)) continue;
			//NameSpacing Ammendment -- Begin
			try {
				if (isset($fieldBlock1['use-class'])) {
					$classname = $fieldBlock1['use-class'];
					if (!is_null($object1)) {
						//We need to acquire a right property
						$oproperty = Registry::getListOfPropertiesOfClass($hostclassname, $classname);
						if (is_null($oproperty)) throw new Exception("Could not get reference object properties");
						$object1 = $object1->getMyPropertyValue($oproperty[0]);
					}
				}
			} catch (Exception $e) {
				$classname = $hostclassname;
				$object1 = $hostobject1;
			}
			//NameSpacing ammendment -- Ending
			$caption = null;
			if (isset($fieldBlock1['caption'])) $caption = $fieldBlock1['caption'];
			else $caption = __object__::property2Caption($pname);
			$required = true;
			if (isset($fieldBlock1['required'])) $required = $fieldBlock1['required'];
			$placeholder = null;
			if (isset($fieldBlock1['placeholder'])) $placeholder = $fieldBlock1['placeholder'];
			$validationColumnName = null;
			if (isset($fieldBlock1['validation-column-name'])) $validationColumnName = $fieldBlock1['validation-column-name'];
			$uiRenderControl = null;
			if (isset($fieldBlock1['ui-control'])) $uiRenderControl = $fieldBlock1['ui-control'];
			//We need to interrupt at this point 

			//We need to Get Control Type 
			$type = Registry::getColumnType($classname, $pname);
			if (isset($fieldBlock1['type'])) $type = $fieldBlock1['type'];
			if (is_null($type)) continue;
			$propertyValue = null;
			if (!is_null($object1)) $propertyValue = $object1->getMyPropertyValue($pname);
			if (isset($fieldBlock1['value'])) $propertyValue = $fieldBlock1['value'];
			if ($type == "object") {
				$line .= self::createFormSelectInput($conn, $classname, $pname, $caption, $propertyValue, $required, $validationColumnName, $fieldBlock1);
			} else if ($type == "text") {
				$line .= self::createFormTextInput($classname, $pname, $caption, $propertyValue, $required, $validationColumnName, $placeholder, $fieldBlock1);
			} else if ($type == "textarea") {
				$line .= self::createFormTextAreaInput($classname, $pname, $caption, $propertyValue, $required, $validationColumnName, $placeholder, $fieldBlock1);
			} else if ($type == "ckeditor") {
				$fieldBlock1['ckeditor'] = true;
				$line .= self::createFormTextAreaInput($classname, $pname, $caption, $propertyValue, $required, $validationColumnName, $placeholder, $fieldBlock1);
			} else if ($type == "file") {
				$line .= self::createFormFileInput($classname, $pname, $caption, $propertyValue, $required, $validationColumnName, $placeholder, $fieldBlock1);
			} else if ($type == "list-object") {
				$line .= self::createFormListSelection($conn, $classname, $pname, $propertyValue, $required, $validationColumnName, $fieldBlock1);
			} else if ($type == "integer") {
				$line .= self::createFormNumberInput($classname, $pname, $caption, $propertyValue, $required, $validationColumnName, $placeholder, $fieldBlock1);
			} else if ($type == "float") {
				$line .= self::createFormNumberInput($classname, $pname, $caption, $propertyValue, $required, $validationColumnName, $placeholder, $fieldBlock1);
			} else if ($type == "boolean") {
				$line .= self::createFormCheckboxInput($classname, $pname, $caption, $propertyValue, $required, $fieldBlock1);
			} else if ($type == "email") {
				$line .= self::createFormEmailInput($classname, $pname, $caption, $propertyValue, $required, $validationColumnName, $placeholder, $fieldBlock1);
			} else if ($type == "date") {
				$line .= self::createFormDateInput($classname, $pname, $caption, $propertyValue, $required, $validationColumnName, $placeholder, $fieldBlock1);
			} else if ($type == "switch-text") {
				$line .= self::createFormSwitchTextInput($classname, $pname, $caption, $propertyValue, $required, $validationColumnName, $placeholder, $fieldBlock1);
			} else if ($type == "switch-select") {
				$line .= self::createFormSwitchSelectInput($conn, $classname, $pname, $caption, $propertyValue, $required, $validationColumnName, $fieldBlock1);
			}
			//echo "\n[ type = $type ] ; ==== [ pname = $pname ]";
		}
		//Now we need to append all grids
		foreach ($listOfGridsByName as $gridName => $listOfFieldBlocks)	{
			$gridCount = $gridRowCountByName[$gridName];
			$serialNumbering = $serialNumberingByName[$gridName];
			$line .= self::createFormDataGrid($conn, $classname, $listOfFieldBlocks, $gridCount, $serialNumbering);
		}
		//This is the best place to process the payload data
		if ($mode == "custom-tabular" && ! is_null($customJSONPayload))	{
			$line .= self::createCustomTabularForm($conn, $hostclassname, $customJSONPayload, $id);
		}
		//Ui Error Field
		$line .= "<div id=\"$errorid\" class=\"p-2 ui-sys-error-message\"></div>";
		$dataServerScript = "";
		if (!is_null($serverScript)) $dataServerScript = "data-server-script = \"$serverScript\"";
		$btnSendWithAjaxClass = "btn-send-dialog-ajax";
		if ($useNormalSubmitButton) {
			$line .= "<div><input id=\"$buttonid\" type=\"submit\" value=\"$buttonText\" class=\"btn btn-primary btn-block btn-click-default $customDataSubmissionClass\"/></div>";
		} else {
			$line .= "<div><button id=\"$buttonid\" $dataServerScript class=\"btn btn-primary btn-block btn-click-default btn-execute-on-click $customDataSubmissionClass\" type=\"button\" data-form-submit=\"$formid\" data-next-page=\"$page\" data-form-error=\"$errorid\">$buttonText</button></div>";
		}
		$line .= "</form>";
		if ($useNormalSubmitButton) {
			//We need to put validation script at this point 
			$line .= "<script type=\"text/javascript\">(function(\$)    {    \$(function()    {        $('#$formid').on('submit', function(e)  {            var button1 = \$('#$buttonid');            var error1 = \$('#$errorid');            if (! window.generalFormValidation(button1, \$(this), error1, Constant)) {                e.preventDefault();                return false;            }            return true;        });    });})(jQuery);</script>";
		}
		$line .= "</div>";
		return $line;
	}
	private static function createASingleListSelection($classname, $refClassname, $name, $caption, $title, $searchText, $targetDiv, $boundColumns, $includeColumnsArray1, $minimumItemsCount, $maximumItemsCount, $requiredString, $disabledString, $filterString, $fplaceholder, $listEmptyMessage, $serverpath)
	{
		//Still More work to be done
		$columnsToInclude = htmlentities(json_encode($includeColumnsArray1));
		//Now proceed -- to develop-UI
		$window1 = "<div class=\"list-object-container border border-primary p-1 m-1\" data-min-length=\"$minimumItemsCount\" data-max-length=\"$maximumItemsCount\" $requiredString $disabledString ><div class=\"list-object-header bg-primary text-white\"><h4>$caption</h4></div><div class=\"list-object-content\">";
		//List Object Content -- Begin
		$window1 .= "<div class=\"list-object-search-container\">";
		//Begin Search Input
		$window1 .= "<input class=\"form-control\" id=\"$searchText\" data-class=\"$refClassname\" data-column='$boundColumns' data-include-column='$columnsToInclude' data-target-container=\"$targetDiv\" $filterString $fplaceholder />";
		//End Search Input
		//Begin Target Container
		$window1 .= "</div><div class=\"list-object-target-container\" style=\"background-color: gray; padding: 2px; margin: 5px;\"><div id=\"$targetDiv\" style=\"margin: 2px; background-color: yellow; padding: 2px;\"><div class=\"m-2 border border-dark bg-danger text-white p-2 text-center\">$listEmptyMessage</div></div></div>";
		//End Target Container
		//List Object Content -- End
		$window1 .= "</div><div class=\"list-object-footer list-object-script\">";
		//Now working on controlObject1  for JavaScript 
		$controlObject1 = array();
		foreach ($includeColumnsArray1 as $pname => $pblock1) {
			foreach ($pblock1 as $key => $valblock1) {
				if ($key == "render-control") {
					//We need to Get Control Type 
					$type = Registry::getColumnType($classname, $pname);
					if (isset($valblock1['type'])) $type = $valblock1['type'];
					if (is_null($type)) break;
					$controlObject1[$pname] = array();
					$controlObject1[$pname]['type'] = $type;
					//Working with validation and data-length
					$dataArray1 = Registry::getUIControlValidationsArray($classname, $pname, $type);
					if (!is_null($dataArray1)) {
						foreach ($dataArray1 as $i => $val) {
							$controlObject1[$pname][$i] = $val;
						}
					}
					//We need to work for other properties
					//title 
					if (isset($valblock1['title'])) {
						$controlObject1[$pname]['title'] = $valblock1['title'];
						$controlObject1[$pname]['data-toggle'] = "tooltip";
					}
					//value 
					if (isset($valblock1['value']))	$controlObject1[$pname]['value'] = $valblock1['value'];
					//disabled
					if (isset($valblock1['disabled'])) $controlObject1[$pname]['disabled'] = $valblock1['disabled'];
					//required 
					$dataIsRequired = "data-is-not-required";
					if (isset($valblock1['required']) && $valblock1['required']) {
						$controlObject1[$pname]['required'] = true;
						$dataIsRequired = "data-is-required";
					}
					$controlObject1[$pname][$dataIsRequired] = true;
					//placeholder 
					if (isset($valblock1['placeholder'])) $controlObject1[$pname]['placeholder'] = $valblock1['placeholder'];
				}
			}
		}
		$controlObject1 = (sizeof($controlObject1) == 0) ? null : json_encode($controlObject1);
		//For JS-Formatting 
		if (is_null($controlObject1)) $controlObject1 = "null";
		//Begin of Script 
		$window1 .= "<script type=\"text/javascript\">    (function(\$)    {        $(function()    {            setCustomAutocomplete('$serverpath', \$('#$searchText'), \$('#$targetDiv'), '$name', 'POST', $controlObject1);        });    })(jQuery);    </script>";
		//End of Script
		$window1 .= "</div></div>";
		return $window1;
	}
	public static function createFormListSelection($conn, $classname, $name, $value = null, $isrequired = true, $validationColumnName = null, $fieldBlock1 = null)
	{
		if (is_null($fieldBlock1)) $fieldBlock1 = array();
		//We need to get a reference-class for this list-object
		$refClassname = Registry::getReferenceClass($classname, $name);
		if (is_null($refClassname)) $refClassname = $classname;
		$caption = isset($fieldBlock1['caption']) ? $fieldBlock1['caption'] : "Selection UI";
		$filter = isset($fieldBlock1['filter']) ? htmlentities(json_encode($fieldBlock1['filter'])) : null;
		$filterOp = isset($fieldBlock1['filter-op']) ? $fieldBlock1['filter-op'] : null;
		$filterString = "";
		if (!is_null($filter)) {
			$filterString = "data-filter = '$filter'";
			if (!is_null($filterOp)) {
				$filterString .= " data-filter-op = '$filterOp'";
			}
		}
		$title = "";
		if (isset($fieldBlock1['title'])) {
			$title = $fieldBlock1['title'];
			$title = " data-toggle = \"tooltip\" title = \"$title\"";
		}
		$listItemCount = (isset($fieldBlock1['list-item']) && isset($fieldBlock1['list-item']['count'])) ? intval($fieldBlock1['list-item']['count']) : 0;
		$listItemCount = $listItemCount > 0 ? $listItemCount : 0;
		//Item Counts
		$minimumItemsCount = 0;
		$minimumItemsCount = isset($fieldBlock1['items-count']) ? (isset($fieldBlock1['items-count']['minimum']) ? $fieldBlock1['items-count']['minimum'] : $minimumItemsCount) : $minimumItemsCount;
		$maximumItemsCount = 99999;
		$maximumItemsCount = isset($fieldBlock1['items-count']) ? (isset($fieldBlock1['items-count']['maximum']) ? $fieldBlock1['items-count']['maximum'] : $maximumItemsCount) : $maximumItemsCount;
		$isrequired = isset($fieldBlock1['required']) ? $fieldBlock1['required'] : false;
		if (intval($minimumItemsCount) == 0 && $isrequired) $minimumItemsCount = 1;
		$requiredString = $isrequired ? "required" : "";
		//Disabled 
		$disabledString = isset($fieldBlock1['disabled']) ? ($fieldBlock1['disabled'] ? "disabled" : "") : "";
		//placeholder
		$placeholder = isset($fieldBlock1['placeholder']) ? $fieldBlock1['placeholder'] : null;
		$fplaceholder = is_null($placeholder) ? "Min Length 3 chars" : $placeholder;
		$fplaceholder = "placeholder=\"$fplaceholder\"";
		//ServerPath
		$serverpath = isset($fieldBlock1['server-path']) ? $fieldBlock1['server-path'] : "../server/getCustomizedListOfRecordsBasedOnCriteria.php";
		//Proceeding
		$prefix = __object__::getMD5CodedString("ListObject", 32);
		$searchText = "__search_" . $prefix . "_ctrl__0001_";
		$targetDiv = "__target_" . $prefix . "_ctrl__0001_";
		//Working With include-columsn
		$includeColumnsArray1 = isset($fieldBlock1['include-columns']) ? $fieldBlock1['include-columns'] : null;
		if (is_null($includeColumnsArray1)) throw new Exception("Columns to be included were not specified");
		//Now working with bound-columns
		$boundColumnsArray1 = isset($fieldBlock1['bound-columns']) ? $fieldBlock1['bound-columns'] : null;
		if (is_null($boundColumnsArray1)) {
			//We need to use the values
			$boundColumnsArray1 = Registry::getValueColumnnames($refClassname);
		}
		if (is_null($boundColumnsArray1)) throw new Exception("Could not get bound columns");
		$boundColumns = htmlentities(json_encode($boundColumnsArray1));
		$listEmptyMessage = self::$__LIST_EMPTY_MESSAGE;
		//UIHere
		$window1 = self::createASingleListSelection($classname, $refClassname, $name, $caption, $title, $searchText, $targetDiv, $boundColumns, $includeColumnsArray1, $minimumItemsCount, $maximumItemsCount, $requiredString, $disabledString, $filterString, $fplaceholder, $listEmptyMessage, $serverpath);
		if ($listItemCount > 0) {
			$window1 = "";
			$bcaption = $caption;
			$btitle = $title;
			for ($i = 0; $i < $listItemCount; $i++) {
				$tname = $name . "[" . $i . "]";
				$tsearchText = $searchText . "_" . $i . "_";
				$ttargetDiv = $targetDiv . "_" . $i . "_";
				$index = $i + 1;
				$title = str_replace(self::$__INDEX_PLACEHOLDER, $index, $btitle);
				$caption = str_replace(self::$__INDEX_PLACEHOLDER, $index, $bcaption);
				$window1 .= self::createASingleListSelection($classname, $refClassname, $tname, $caption, $title, $tsearchText, $ttargetDiv, $boundColumns, $includeColumnsArray1, $minimumItemsCount, $maximumItemsCount, $requiredString, $disabledString, $filterString, $fplaceholder, $listEmptyMessage, $serverpath);
			}
		}
		return $window1;
	}
	public static function createFormTextAreaInput($classname, $name, $caption, $value = null, $isrequired = true, $validationColumnName = null, $placeholder = null, $fieldBlock1 = null)
	{
		$otherproperties = "";
		$title = "";
		if (isset($fieldBlock1['disabled'])) $otherproperties = " disabled";
		if (isset($fieldBlock1['title'])) {
			$ltitle = $fieldBlock1['title'];
			$title = " data-toggle = \"tooltip\" title = \"$ltitle\"";
			$otherproperties .= $title;
		}
		if (isset($fieldBlock1['readonly']) && $fieldBlock1['readonly'])	{
			$otherproperties .= " readonly";
		}
		$listItemCount = (isset($fieldBlock1['list-item']) && isset($fieldBlock1['list-item']['count'])) ? intval($fieldBlock1['list-item']['count']) : 0;
		$listItemCount = $listItemCount > 0 ? $listItemCount : 0;
		$cols = isset($fieldBlock1['cols']) ? intval($fieldBlock1['cols']) : 30;
		$rows = isset($fieldBlock1['rows']) ? intval($fieldBlock1['rows']) : 5;
		$isCkeditor = isset($fieldBlock1['ckeditor']) ? $fieldBlock1['ckeditor'] : false;
		$required = "data-is-not-required=\"true\"";
		if ($isrequired) $required = "required data-is-required=\"true\"";
		$fvalue = "";
		if (!is_null($value)) $fvalue = "value = \"$value\"";
		$fplaceholder = "";
		if (!is_null($placeholder)) $fplaceholder = "placeholder = \"$placeholder\"";
		if (is_null($validationColumnName)) $validationColumnName = $name;
		$validationRule = Registry::getUIControlValidations($classname, $validationColumnName, "text"); //All Inputs are validated as text , unless otherwise, select control not included
		$id = self::name2id($name);
		$controlOnly = "<textarea cols=\"$cols\" rows=\"$rows\" $otherproperties class=\"form-control\" id=\"$id\" name=\"$name\" $fvalue $required $fplaceholder $validationRule></textarea>";
		$controlSeparator = (isset($fieldBlock1['control-separator'])) ? $fieldBlock1['control-separator'] : " ";
		$uiline = "<div class=\"form-group\"><label $title for=\"$id\" class=\"form-label\">$caption</label><div>$controlOnly</div></div>";
		if ($isCkeditor) {
			//$uiline .= "<script>(function(\$)    {    \$(function()    {        CKEDITOR.replace('$name');    });})(jQuery);</script>";
		}
		if ($listItemCount > 0) {
			$uiline = "";
			$bcaption = $caption;
			$btitle = $title;
			$controlOnly = "";
			for ($i = 0; $i < $listItemCount; $i++) {
				$id = $name . "_" . $i . "_";
				$tname = $name . "[" . $i . "]";
				$index = $i + 1;
				$title = str_replace(self::$__INDEX_PLACEHOLDER, $index, $btitle);
				$caption = str_replace(self::$__INDEX_PLACEHOLDER, $index, $bcaption);
				$tcontrolOnly = "<textarea cols=\"$cols\" rows=\"$rows\" $otherproperties class=\"form-control\" id=\"$id\" name=\"$tname\" $fvalue $required $fplaceholder $validationRule></textarea>";
				$uiline .= "<div class=\"form-group\"><label $title for=\"$id\" class=\"form-label\">$caption</label><div>$tcontrolOnly</div></div>";
				if ($isCkeditor) {
					//$uiline .= "<script>(function(\$)    {    \$(function()    {        CKEDITOR.replace('$id');    });})(jQuery);</script>";
				}
				$controlOnly = ($i == 0) ? $tcontrolOnly : ($controlOnly . $controlSeparator . $tcontrolOnly);
			}
		}
		return ((isset($fieldBlock1['control-only']) && $fieldBlock1['control-only']) ? $controlOnly : $uiline);
	}
	public static function createFormFileInput($classname, $name, $caption, $value = null, $isrequired = true, $validationColumnName = null, $placeholder = null, $fieldBlock1 = null)
	{
		$otherproperties = "";
		$title = "";
		if (isset($fieldBlock1['disabled'])) $otherproperties = " disabled";
		if (isset($fieldBlock1['title'])) {
			$ltitle = $fieldBlock1['title'];
			$title = " data-toggle = \"tooltip\" title = \"$ltitle\"";
			$otherproperties .= $title;
		}
		$listItemCount = (isset($fieldBlock1['list-item']) && isset($fieldBlock1['list-item']['count'])) ? intval($fieldBlock1['list-item']['count']) : 0;
		$listItemCount = $listItemCount > 0 ? $listItemCount : 0;
		$required = "";
		if ($isrequired) $required = "required";
		$fvalue = "";
		if (!is_null($value)) $fvalue = "value = \"$value\"";
		$fplaceholder = "";
		if (!is_null($placeholder)) $fplaceholder = "placeholder = \"$placeholder\"";
		if (is_null($validationColumnName)) $validationColumnName = $name;
		$validationRule = Registry::getUIControlValidations($classname, $validationColumnName, "text"); //All Inputs are validated as text , unless otherwise, select control not included
		$id = self::name2id($name);
		$controlOnly = "<input type=\"file\"	 $otherproperties class=\"form-control\" id=\"$id\" name=\"$name\" $fvalue $required $fplaceholder $validationRule />";
		$uiline = "<div class=\"form-group\"><label $title for=\"$id\" class=\"form-label\">$caption</label><div>$controlOnly</div></div>";
		if ($listItemCount > 0) {
			$uiline = "";
			$bcaption = $caption;
			$btitle = $title;
			$controlOnly = "";
			$controlSeparator = $fieldBlock1['control-separator'] ? $fieldBlock1['control-separator'] : " ";
			for ($i = 0; $i < $listItemCount; $i++) {
				$id = $name . "_" . $i . "_";
				$tname = $name . "[" . $i . "]";
				$index = $i + 1;
				$title = str_replace(self::$__INDEX_PLACEHOLDER, $index, $btitle);
				$caption = str_replace(self::$__INDEX_PLACEHOLDER, $index, $bcaption);
				$tcontrolOnly = "<input type=\"file\"	 $otherproperties class=\"form-control\" id=\"$id\" name=\"$tname\" $fvalue $required $fplaceholder $validationRule />";
				$uiline .= "<div class=\"form-group\"><label $title for=\"$id\" class=\"form-label\">$caption</label><div>$tcontrolOnly</div></div>";
				$controlOnly = ($i == 0) ? $tcontrolOnly : ($controlOnly . $controlSeparator . $tcontrolOnly);
			}
		}
		return $uiline;
	}
	private static function createASingleSelectInput($classname, $id, $name, $caption, $refObject1, $title, $validationColumnName, $validationRule, $required, $otherproperties, $defaultValue, $dataArray1, $fieldBlock1, $indexed = -1)
	{
		$controlOnly = "<select $otherproperties class=\"form-control\" id=\"$id\" name=\"$name\" $required $validationRule><option value=\"$defaultValue\">(-- Select --)</option>";
		if (is_null($validationColumnName)) $validationColumnName = $name;
		if (!is_null($dataArray1)) {
			foreach ($dataArray1 as $key => $dataBlock1) {
				$dvalue = $dataBlock1['__option_value__'];
				$dcaption = $dataBlock1['__option_caption__'];
				if (!$dataBlock1['__display__']) continue;
				//RefObject
				$selected = "";
				if (!is_null($refObject1) && ($dvalue == $refObject1->getId0())) $selected = "selected";
				$controlOnly .= "<option $selected value=\"$dvalue\">$dcaption</option>";
			}
		}
		$controlOnly .= "</select>";
		$control1 =  "<div class=\"form-group row\"><label $title for=\"$id\" class=\"col-sm-2 col-form-label\">$caption</label><div class=\"col-sm-10\">$controlOnly</div></div>";
		//Now dealing with cascade 
		if (isset($fieldBlock1['cascade']) && isset($fieldBlock1['cascade']['parent'])) {
			//Step 1 get classname and pname
			$pname1 = $fieldBlock1['pname'];
			$classname1 = $classname;
			$id1 = $id;
			//parent 
			$pname2 = $fieldBlock1['cascade']['parent'];
			$classname2 = $classname;
			//Now work if difference classname
			$index = isset($fieldBlock1['__pname_index__'][$pname2]) ? $fieldBlock1['__pname_index__'][$pname2] : null;
			if (!is_null($index) && isset($fieldBlock1['__payload__'][$index])) {
				$fieldBlock2 = $fieldBlock1['__payload__'][$index];
				if (isset($fieldBlock2['use-class'])) $classname2 = $fieldBlock2['use-class'];
			}
			$id2 = ($indexed == -1) ? $pname2 : ($pname2 . "_" . $indexed . "_");
			//Step 2: Get classes of origin
			$classname1 = Registry::getReferenceClass($classname1, $pname1);
			$classname2 = Registry::getReferenceClass($classname2, $pname2);
			if (is_null($classname1) || is_null($classname2)) return $control1;
			//Step 3: Check if child(1) really refer to parent 
			$referenceColumns = Registry::getListOfPropertiesOfClass($classname1, $classname2);
			if (is_null($referenceColumns)) return $control1;
			//Build Script 
			$referenceColumns = json_encode($referenceColumns);
			$format = isset($fieldBlock1['format']) ? $fieldBlock1['format'] : null;
			if (!is_null($format)) $format = "\"$format\"";
			$serverpath = isset($fieldBlock1['server-path']) ? $fieldBlock1['server-path'] : "../server/cascade_get_payload.php";
			$scriptBody1 = "\$('#$id2').on('change', function(e)	{ var selectedId = this.value;if ( this.value == Constant.default_select_empty_value ) return false;var \$child1 = $('#$id1');if (! \$child1.length) return false;populateCascadeSelect(\$(this), \"$classname2\", selectedId, \$child1, \"$classname1\", $referenceColumns, $format, \"$serverpath\", Constant); });";
			$t1 = "<script type=\"text/javaScript\">(function(\$)	{ \$(function()	{ $scriptBody1 }); })(jQuery);</script>";
			$controlOnly .= $t1;
			$control1 .= $t1;
		}
		return $control1;
		return ((isset($fieldBlock1['control-only']) && $fieldBlock1['control-only']) ? $controlOnly : $control1);
	}
	public static function createFormSelectInput($conn1, $classname, $name, $caption, $refObject1 = null, $isrequired = true, $validationColumnName = null, $fieldBlock1 = null)
	{
		$otherproperties = "";
		$title = "";
		if (isset($fieldBlock1['disabled'])) $otherproperties = " disabled";
		if (isset($fieldBlock1['title'])) {
			$ltitle = $fieldBlock1['title'];
			$title = " data-toggle = \"tooltip\" title = \"$ltitle\"";
			$otherproperties .= $title;
		}
		$listItemCount = (isset($fieldBlock1['list-item']) && isset($fieldBlock1['list-item']['count'])) ? intval($fieldBlock1['list-item']['count']) : 0;
		$listItemCount = $listItemCount > 0 ? $listItemCount : 0;
		$required = "";
		if ($isrequired) $required = "required";
		$validationRule = Registry::getUIControlValidations($classname, $name, "select"); //All Inputs are validated as text , unless otherwise, select control not included
		$defaultValue = Constant::$default_select_empty_value;
		//Begin UI Control
		if (is_null($validationColumnName)) $validationColumnName = $name;
		$refClassname = Registry::getReferenceClass($classname, $validationColumnName);
		$dataArray1 = null;
		//Let see if format were set
		$listOfColumnPlaceholder = array();
		$listOfColumns = null;
		$format = null;
		if (isset($fieldBlock1['format']))	{
			$format = $fieldBlock1['format'];
			$listOfColumns = __data__::getListOfColumns($refClassname, $format, $listOfColumnPlaceholder);
		}
		$formatEnabled = ( ! is_null($format) &&  ! is_null($listOfColumns) );
		if (!is_null($refClassname) && !isset($fieldBlock1['cascade'])) {
			if ($formatEnabled)	{
				$dataArray1 = Registry::loadAllCustomizedData($conn1, $refClassname, $listOfColumns);
			} else {
				$dataArray1 = Registry::loadAllData($conn1, $refClassname);
			}
		}
		if (!is_null($dataArray1)) {
			foreach ($dataArray1 as $key => $dataBlock1) {
				$dvalue = $dataBlock1['__id__'];
				$dcaption = $dataBlock1['__name__'];
				if ($formatEnabled)	{
					$dcaption = $format; 
					//Now do replacement
					foreach ($listOfColumns as $col)	{
						$plc = $listOfColumnPlaceholder[$col];
						$val = $dataBlock1[$col];
						$dcaption = str_replace($plc, $val, $dcaption);
					}
				}
				$showInSelectControl = true;
				if (isset($fieldBlock1['filter'])) {
					$opNot = false;
					if (isset($fieldBlock1['filter-op']) && $fieldBlock1['filter-op'] == "not") $opNot = true;
					foreach ($fieldBlock1['filter'] as $fpname => $valueArray1) {
						$fobject1 = Registry::getObjectReference("Hello", $conn1, $refClassname, $dvalue);
						if (is_null($fobject1)) continue;
						$f_ext_value = $fobject1->getMyPropertyValue($fpname); //Assume object like Sex, we need to call getId0()
						$refClassname02 = Registry::getReferenceClass($refClassname, $fpname);
						if (!is_null($refClassname02) && !in_array($refClassname02, array('DateAndTime'))) {
							$f_ext_value = $f_ext_value->getId0();
						}
						//Working with booleans 
						if (gettype($f_ext_value) == "boolean") {
							if ($f_ext_value) $f_ext_value = 1;
							else $f_ext_value = 0;
						}
						/*if (! is_null($f_ext_value) && ! in_array($f_ext_value, $valueArray1)) {
							$showInSelectControl = false;
							break;
						}*/
						$a = is_null($f_ext_value);
						$b = in_array($f_ext_value, $valueArray1);
						$c = $opNot;
						$showInSelectControl = $b && !$c || !$a && !$b && $c || $a && !$c;
						if (!$showInSelectControl) break;
					}
				}
				if (!$showInSelectControl) continue;
				$dataArray1[$key]['__display__'] = $showInSelectControl;
				$dataArray1[$key]['__option_value__'] = $dvalue;
				$dataArray1[$key]['__option_caption__'] = $dcaption;
			}
		}
		//End UI Control
		$id = self::name2id($name);
		$control1 = self::createASingleSelectInput($classname, $id, $name, $caption, $refObject1, $title, $validationColumnName, $validationRule, $required, $otherproperties, $defaultValue, $dataArray1, $fieldBlock1);
		$iscontrolOnly = (isset($fieldBlock1['control-only']) && $fieldBlock1['control-only']);
		if ($listItemCount > 0) {
			$control1 = "";
			$bcaption = $caption;
			$btitle = $title;
			$controlSeparator = isset($fieldBlock1['control-separator']) ? $fieldBlock1['control-separator'] : " ";
			for ($i = 0; $i < $listItemCount; $i++) {
				$id = $name . "_" . $i . "_";
				$tname = $name . "[" . $i . "]";
				$index = $i + 1;
				$title = str_replace(self::$__INDEX_PLACEHOLDER, $index, $btitle);
				$caption = str_replace(self::$__INDEX_PLACEHOLDER, $index, $bcaption);
				$t1 = self::createASingleSelectInput($classname, $id, $tname, $caption, $refObject1, $title, $validationColumnName, $validationRule, $required, $otherproperties, $defaultValue, $dataArray1, $fieldBlock1, $i);
				if ($iscontrolOnly) {
					$control1 = ($control1 . $controlSeparator . $t1);
				} else {
					$control1 .= $t1;
				}
			}
		}
		return $control1;
	}
	public static function createFormDataGrid($conn, $classname, $listOfFieldBlocks, $rowcount = 1, $serialNumbering = true)
	{
		if ($rowcount < 1) return "";
		$tabledata = "";
		$tableheader = $serialNumbering ? "<th scope=\"col\">S/N</th>" : "";
		$hiddenfields = "";
		for ($i = 0; $i < $rowcount; $i++) {
			$sn = $i + 1;
			$rowdata = $serialNumbering ? "<th scope=\"row\">$sn</th>" : "";
			foreach ($listOfFieldBlocks as $key => $fieldBlock1) {
				//Header 
				$name = isset($fieldBlock1['pname']) ? $fieldBlock1['pname'] : null;
				if (is_null($name)) {
					$rowdata .= "<td></td>";
					continue;
				}
				$caption = isset($fieldBlock1['caption']) ? $fieldBlock1['caption'] : (__object__::property2Caption($name));
				$required = (isset($fieldBlock1['required']) && $fieldBlock1['required']) ? true : false;
				$tclassname = isset($fieldBlock1['use-class']) ? $fieldBlock1['use-class'] : $classname;
				$value = isset($fieldBlock1['value']) ? $fieldBlock1['value'] : null;
				$validationColumnName = isset($fieldBlock1['validation-column-name']) ? $fieldBlock1['validation-column-name'] : null;
				$placeholder = isset($fieldBlock1['placeholder']) ? $fieldBlock1['placeholder'] : null;

				if ($i == 0) {
					$tableheader .= "<th scope=\"col\">$caption</th>";
				}
				//Update the fieldBlock1 
				$fieldBlock1['control-only'] = true;
				//get-type
				$type = Registry::getColumnType($tclassname, $name);
				$type = isset($fieldBlock1['type']) ? $fieldBlock1['type'] : $type;
				if (is_null($type)) {
					$rowdata .= "<td></td>";
					continue;
				}
				$pname = $name. "[" . $i . "]";
				$t1 = "";
				switch ($type)	{
					case "object":
						$t1 = self::createFormSelectInput($conn, $tclassname, $pname, $caption, null, $required, $validationColumnName, $fieldBlock1);
						break;
					case "text":
						$t1 = self::createFormTextInput($tclassname, $pname, $caption, $value, $required, $validationColumnName, $placeholder, $fieldBlock1);
						break;
					case "textarea":
						$t1 = self::createFormTextAreaInput($tclassname, $pname, $caption, $value, $required, $validationColumnName, $placeholder, $fieldBlock1);
						break;
					case "ckeditor":
						$fieldBlock1['ckeditor'] = true;
						$t1 = self::createFormTextAreaInput($tclassname, $pname, $caption, $value, $required, $validationColumnName, $placeholder, $fieldBlock1);
						break;
					case "file":
						$t1 = self::createFormFileInput($tclassname, $pname, $caption, $value, $required, $validationColumnName, $placeholder, $fieldBlock1);
						break;
					case "integer":
						$t1 = self::createFormNumberInput($tclassname, $pname, $caption, $value, $required, $validationColumnName, $placeholder, $fieldBlock1);
						break;
					case "float":
						$t1 = self::createFormNumberInput($tclassname, $pname, $caption, $value, $required, $validationColumnName, $placeholder, $fieldBlock1);
						break;
					case "email":
						$t1 = self::createFormEmailInput($tclassname, $pname, $caption, $value, $required, $validationColumnName, $placeholder, $fieldBlock1);
						break;
					case "switch-text":
						$t1 = self::createFormSwitchTextInput($tclassname, $pname, $caption, $value, $required, $validationColumnName, $placeholder, $fieldBlock1);
						break;
					case "switch-select":
						$t1 = self::createFormSelectInput($conn, $tclassname, $pname, $caption, null, $required, $validationColumnName, $fieldBlock1);
						break;
				}
				$rowdata .= "<td>$t1</td>";
			}
			$tabledata .= "<tr>$rowdata</tr>";
		}
		$tableheader = "<tr>$tableheader</tr>";
		//Working with footer
		$colspan = sizeof($listOfFieldBlocks) + ( $serialNumbering ? 1 : 0 );
		$footerdata = "<tr><td colspan=\"$colspan\">$hiddenfields</td></tr>";
		$window1 = "<div class=\"my-1\"><div class=\"table-responsive\"><table class=\"table\"><thead>$tableheader</thead><tbody>$tabledata</tbody><tfooter>$footerdata</tfooter></table></div></div>";
		return $window1;
	}
	public static function createFormPasswordInput($classname, $name, $caption, $value = null, $isrequired = true, $validationColumnName = null, $placeholder = null, $fieldBlock1 = null)
	{
		return self::createGeneralFormInput($classname, $name, $caption, $value, $isrequired, "password", $validationColumnName, $placeholder, $fieldBlock1);
	}
	public static function createFormNumberInput($classname, $name, $caption, $value = null, $isrequired = true, $validationColumnName = null, $placeholder = null, $fieldBlock1 = null)
	{
		return self::createGeneralFormInput($classname, $name, $caption, $value, $isrequired, "number", $validationColumnName, $placeholder, $fieldBlock1);
	}
	public static function createFormEmailInput($classname, $name, $caption, $value = null, $isrequired = true, $validationColumnName = null, $placeholder = null, $fieldBlock1 = null)
	{
		return self::createGeneralFormInput($classname, $name, $caption, $value, $isrequired, "email", $validationColumnName, $placeholder, $fieldBlock1);
	}
	public static function createFormTextInput($classname, $name, $caption, $value = null, $isrequired = true, $validationColumnName = null, $placeholder = null, $fieldBlock1 = null)
	{
		return self::createGeneralFormInput($classname, $name, $caption, $value, $isrequired, "text", $validationColumnName, $placeholder, $fieldBlock1);
	}
	public static function createFormSwitchSelectInput($conn1, $classname, $name, $caption, $refObject1 = null, $isrequired = true, $validationColumnName = null, $fieldBlock1 = null)
	{
		$otherproperties = "";
		$title = "";
		if (isset($fieldBlock1['disabled'])) $otherproperties = " disabled";
		if (isset($fieldBlock1['title'])) {
			$ltitle = $fieldBlock1['title'];
			$title = " data-toggle = \"tooltip\" title = \"$ltitle\"";
			$otherproperties .= $title;
		}
		$listItemCount = (isset($fieldBlock1['list-item']) && isset($fieldBlock1['list-item']['count'])) ? intval($fieldBlock1['list-item']['count']) : 0;
		$listItemCount = $listItemCount > 0 ? $listItemCount : 0;
		$checked = "";
		if (isset($fieldBlock1['checked'])) $checked = "checked";
		$required = "";
		if ($isrequired) $required = "required";
		if (is_null($validationColumnName)) $validationColumnName = $name;
		$validationRule = Registry::getUIControlValidations($classname, $validationColumnName, "select"); //All Inputs are validated as text , unless otherwise, select control not included
		$window1 = "<div $title class=\"form-group row\">";
		$switchId = self::name2id("__switch_select_$name");
		//$window1 .= "<label $title for=\"$name\" class=\"col-sm-2 col-form-label\">$caption</label>";
		$window1 .= "<div class=\"col-sm-2\"><input id=\"$switchId\" type=\"checkbox\"  $checked data-bootstrap-switch data-off-color=\"danger\" data-on-color=\"success\"/></div>";
		$window1 .= "<div class=\"col-sm-2\"><span style=\"font-size: 0.9em;\">$caption</span></div>";
		$defaultValue = Constant::$default_select_empty_value;
		$window1 .= "<div class=\"col-sm-8\"><select $otherproperties class=\"form-control\" id=\"$name\" name=\"$name\" $required $validationRule><option value=\"$defaultValue\">(-- Select --)</option>";
		if (is_null($validationColumnName)) $validationColumnName = $name;
		$refClassname = Registry::getReferenceClass($classname, $validationColumnName);
		$dataArray1 = null;
		if (!is_null($refClassname)) $dataArray1 = Registry::loadAllData($conn1, $refClassname);
		if (!is_null($dataArray1)) {
			foreach ($dataArray1 as $dataBlock1) {
				$dvalue = $dataBlock1['__id__'];
				$dcaption = $dataBlock1['__name__'];
				$selected = "";
				if (!is_null($refObject1) && ($dvalue == $refObject1->getId0())) $selected = "selected";
				$window1 .= "<option $selected value=\"$dvalue\">$dcaption</option>";
			}
		}
		$window1 .= "</select></div>";
		$window1 .= "</div>";  //Close .form-group.row
		$window1 .= "<script type=\"text/javascript\">    $('#$switchId').on('change.bootstrapSwitch', function(e) {       $('#$name').prop('disabled', ! e.target.checked);      });</script>";
		return $window1;
	}
	public static function createFormSwitchTextInput($classname, $name, $caption, $value = null, $isrequired = true, $validationColumnName = null, $placeholder = null, $fieldBlock1 = null)
	{
		$otherproperties = "";
		$title = "";
		if (isset($fieldBlock1['disabled'])) $otherproperties = " disabled";
		if (isset($fieldBlock1['title'])) {
			$ltitle = $fieldBlock1['title'];
			$title = " data-toggle = \"tooltip\" title = \"$ltitle\"";
			$otherproperties .= $title;
		}
		$listItemCount = (isset($fieldBlock1['list-item']) && isset($fieldBlock1['list-item']['count'])) ? intval($fieldBlock1['list-item']['count']) : 0;
		$listItemCount = $listItemCount > 0 ? $listItemCount : 0;
		$checked = "";
		if (isset($fieldBlock1['checked'])) $checked = "checked";
		$required = "";
		if ($isrequired) $required = "required";
		$fvalue = "";
		if (!is_null($value)) $fvalue = "value = \"$value\"";
		$fplaceholder = "";
		if (!is_null($placeholder)) $fplaceholder = "placeholder = \"$placeholder\"";
		if (is_null($validationColumnName)) $validationColumnName = $name;
		$validationRule = Registry::getUIControlValidations($classname, $validationColumnName, "text"); //All Inputs are validated as text , unless otherwise, select control not included
		$switchId = self::name2id("__switch_text_$name");
		$window1 = "<div class=\"form-group row\">";
		$window1 .= "<div $title class=\"col-sm-2\"><input id=\"$switchId\" type=\"checkbox\"  $checked data-bootstrap-switch data-off-color=\"danger\" data-on-color=\"success\"/></div>";
		$window1 .= "<div class=\"col-sm-10\"><input $otherproperties class=\"form-control\" id=\"$name\" name=\"$name\" type=\"text\" $fvalue $required $fplaceholder $validationRule /></div></div>";
		$window1 .= "<script type=\"text/javascript\">    $('#$switchId').on('change.bootstrapSwitch', function(e) {       $('#$name').prop('disabled', ! e.target.checked);      });</script>";
		if ($listItemCount > 0) {
			$window1 = "";
			$bcaption = $caption;
			$btitle = $title;
			for ($i = 0; $i < $listItemCount; $i++) {
				$id = $name . "__" . $i;
				$tname = $name . "[" . $i . "]";
				$tswitchId = $switchId . "__" . $i;
				$index = $i + 1;
				$title = str_replace(self::$__INDEX_PLACEHOLDER, $index, $btitle);
				$caption = str_replace(self::$__INDEX_PLACEHOLDER, $index, $bcaption);
				$window1 .= "<div class=\"form-group row\">";
				$window1 .= "<div $title class=\"col-sm-2\"><input id=\"$tswitchId\" type=\"checkbox\"  $checked data-bootstrap-switch data-off-color=\"danger\" data-on-color=\"success\"/></div>";
				$window1 .= "<div class=\"col-sm-10\"><input $otherproperties class=\"form-control\" id=\"$id\" name=\"$tname\" type=\"text\" $fvalue $required $fplaceholder $validationRule /></div></div>";
				$window1 .= "<script type=\"text/javascript\">    $('#$tswitchId').on('change.bootstrapSwitch', function(e) {       $('#$id').prop('disabled', ! e.target.checked);      });</script>";
			}
		}
		return $window1;
	}
	public static function createFormDateInput($classname, $name, $caption, $value = null, $isrequired = true, $validationColumnName = null, $placeholder = null, $fieldBlock1 = null)
	{
		//$window1 = self::createGeneralFormInput($classname, $name, $caption, $value, $isrequired, "date", $validationColumnName, $placeholder);
		$otherproperties = "";
		$title = "";
		if (isset($fieldBlock1['disabled'])) $otherproperties = " disabled";
		if (isset($fieldBlock1['title'])) {
			$ltitle = $fieldBlock1['title'];
			$title = " data-toggle = \"tooltip\" title = \"$ltitle\"";
			$otherproperties .= $title;
		}
		$listItemCount = (isset($fieldBlock1['list-item']) && isset($fieldBlock1['list-item']['count'])) ? intval($fieldBlock1['list-item']['count']) : 0;
		$listItemCount = $listItemCount > 0 ? $listItemCount : 0;
		$required = "";
		if ($isrequired) $required = "required";
		$fvalue = "";
		if (!is_null($value)) {
			try {
				$value = $value->getGUIDateOnlyFormat();
			} catch (Exception $e) {
			}
			$fvalue = "value = \"$value\"";
		}
		$fplaceholder = "";
		if (!is_null($placeholder)) $fplaceholder = "placeholder = \"$placeholder\"";
		if (is_null($validationColumnName)) $validationColumnName = $name;
		$validationRule = Registry::getUIControlValidations($classname, $validationColumnName, "text"); //All Inputs are validated as text , unless otherwise, select control not included
		$randId = __object__::getMD5CodedString();
		$id = self::name2id($name);
		$window1 =  "<div class=\"form-group row\"><label $title class=\"col-sm-2 col-form-label\"for=\"$id\">$caption</label><div class=\"col-sm-10 input-group date\" id=\"$randId\" data-target-input=\"nearest\"><input $otherproperties class=\"form-control datetimepicker-input\" data-target=\"#$randId\" id=\"$id\" name=\"$name\" type=\"text\" $fvalue $required $fplaceholder $validationRule /><div class=\"input-group-append\" data-target=\"#$randId\" data-toggle=\"datetimepicker\"><div class=\"input-group-text\"><i class=\"fa fa-calendar\"></i></div></div></div></div>";
		$window1 .= "<script>\$('#$randId').datetimepicker({ format: 'L' });</script>";
		/*$window1 .= "<script type=\"text/javascript\">\$('#$randId').datepicker({ format: 'dd/mm/yyyy' });</script>";*/
		if ($listItemCount > 0) {
			$window1 = "";
			$bcaption = $caption;
			$btitle = $title;
			for ($i = 0; $i < $listItemCount; $i++) {
				$id = $name . "__" . $i;
				$tname = $name . "[" . $i . "]";
				$trandId = __object__::getMD5CodedString($i, 32);
				$index = $i + 1;
				$title = str_replace(self::$__INDEX_PLACEHOLDER, $index, $btitle);
				$caption = str_replace(self::$__INDEX_PLACEHOLDER, $index, $bcaption);
				$window1 .=  "<div class=\"form-group row\"><label $title class=\"col-sm-2 col-form-label\"for=\"$id\">$caption</label><div class=\"col-sm-10 input-group date\" id=\"$trandId\" data-target-input=\"nearest\"><input $otherproperties class=\"form-control datetimepicker-input\" data-target=\"#$trandId\" id=\"$id\" name=\"$tname\" type=\"text\" $fvalue $required $fplaceholder $validationRule /><div class=\"input-group-append\" data-target=\"#$trandId\" data-toggle=\"datetimepicker\"><div class=\"input-group-text\"><i class=\"fa fa-calendar\"></i></div></div></div></div>";
				$window1 .= "<script>\$('#$trandId').datetimepicker({ format: 'L' });</script>";
			}
		}
		return $window1; //Solving updation
	}
	public static function createFormCheckboxInput($classname, $name, $caption, $checked = false, $isrequired = true, $fieldBlock1 = null)
	{
		if (is_null($checked)) $checked = false;
		$otherproperties = "";
		$title = "";
		if (isset($fieldBlock1['disabled'])) $otherproperties = " disabled";
		if (isset($fieldBlock1['title'])) {
			$ltitle = $fieldBlock1['title'];
			$title = " data-toggle = \"tooltip\" title = \"$ltitle\"";
			$otherproperties .= $title;
		}
		$listItemCount = (isset($fieldBlock1['list-item']) && isset($fieldBlock1['list-item']['count'])) ? intval($fieldBlock1['list-item']['count']) : 0;
		$listItemCount = $listItemCount > 0 ? $listItemCount : 0;
		$required = "";
		if ($isrequired) $required = "required";
		if ($checked) {
			$otherproperties .= " checked";
		}
		$gchkname = self::$__CHECKBOX_NAME;
		$gchkname .= "[" . $name . "]";
		$id = self::name2id($name);
		$window1 =  "<div $title style=\"width: 100%;\" class=\"form-check form-switch\"><input type=\"checkbox\" class=\"form-check-input\" id=\"$id\" name=\"$name\" value=\"1\" $otherproperties /><label class=\"form-check-label\" for=\"$id\">$caption</label><input type=\"hidden\" name=\"$gchkname\" value=\"0\"/></div>";
		if ($listItemCount > 0) {
			$window1 = "";
			$bcaption = $caption;
			$btitle = $title;
			for ($i = 0; $i < $listItemCount; $i++) {
				$id = $name . "_" . $i . "_";
				$tname = $name . "[" . $i . "]";
				$index = $i + 1;
				$gchkname = self::$__CHECKBOX_NAME;
				$gchkname .= "[" . $i . "][" . $name . "]";
				$title = str_replace(self::$__INDEX_PLACEHOLDER, $index, $btitle);
				$caption = str_replace(self::$__INDEX_PLACEHOLDER, $index, $bcaption);
				$window1 .=  "<div $title style=\"width: 100%;\" class=\"form-check form-switch\"><input type=\"checkbox\" class=\"form-check-input\" id=\"$id\" name=\"$tname\" value=\"1\" $otherproperties /><label class=\"form-check-label\" for=\"$id\">$caption</label><input type=\"hidden\" name=\"$gchkname\" value=\"0\"/></div>";
			}
		}
		return $window1;
	}
	public static function createGeneralFormInput($classname, $name, $caption, $value = null, $isrequired = true, $type = "text", $validationColumnName = null, $placeholder = null, $fieldBlock1 = null)
	{
		$otherproperties = "";
		$title = "";
		if (isset($fieldBlock1['disabled'])) $otherproperties = " disabled";
		if (isset($fieldBlock1['title'])) {
			$ltitle = $fieldBlock1['title'];
			$title = " data-toggle = \"tooltip\" title = \"$ltitle\"";
			$otherproperties .= $title;
		}
		if (isset($fieldBlock1['readonly']) && $fieldBlock1['readonly'])	{
			$otherproperties .= " readonly";
		}
		$listItemCount = (isset($fieldBlock1['list-item']) && isset($fieldBlock1['list-item']['count'])) ? intval($fieldBlock1['list-item']['count']) : 0;
		$listItemCount = $listItemCount > 0 ? $listItemCount : 0;
		$required = "data-is-not-required=\"true\"";
		if ($isrequired) $required = "required data-is-required=\"true\"";
		$fvalue = "";
		if (!is_null($value)) $fvalue = "value = \"$value\"";
		$fplaceholder = "";
		if (!is_null($placeholder)) $fplaceholder = "placeholder = \"$placeholder\"";
		if (is_null($validationColumnName)) $validationColumnName = $name;
		$validationRule = Registry::getUIControlValidations($classname, $validationColumnName, "text"); //All Inputs are validated as text , unless otherwise, select control not included
		$id = self::name2id($name);
		$controlOnly = "<input $otherproperties class=\"form-control\" id=\"$id\" name=\"$name\" type=\"$type\" $fvalue $required $fplaceholder $validationRule />";
		$window1 = "<div class=\"form-group row\"><label $title for=\"$id\" class=\"col-sm-2 col-form-label\">$caption</label><div class=\"col-sm-10\">$controlOnly</div></div>";
		if ($listItemCount > 0) {
			$window1 = "";
			$bcaption = $caption;
			$btitle = $title;
			$tcontrolOnly = "";
			$controlOnly = "";
			$controlSeparator = isset($fieldBlock1['control-separator']) ? $fieldBlock1['control-separator'] : "";
			for ($i = 0; $i < $listItemCount; $i++) {
				$id = $name . "__" . $i;
				$tname = $name . "[" . $i . "]";
				$index = $i + 1;
				$title = str_replace(self::$__INDEX_PLACEHOLDER, $index, $btitle);
				$caption = str_replace(self::$__INDEX_PLACEHOLDER, $index, $bcaption);
				$tcontrolOnly = "<input $otherproperties class=\"form-control\" id=\"$id\" name=\"$tname\" type=\"$type\" $fvalue $required $fplaceholder $validationRule />";
				$controlOnly = ($i == 0) ? $tcontrolOnly : ($controlOnly . $controlSeparator . $tcontrolOnly);
				$window1 .= "<div class=\"form-group row\"><label $title for=\"$id\" class=\"col-sm-2 col-form-label\">$caption</label><div class=\"col-sm-10\">$tcontrolOnly</div></div>";
			}
		}
		return ((isset($fieldBlock1['control-only']) && $fieldBlock1['control-only']) ? $controlOnly : $window1);
	}
	//01.Flags Management 
	public function setFlagAt($pos)
	{
		$powerOfTwo = __object__::getPowerOfTwo($pos);
		if ($powerOfTwo != 0) {
			$flagValue = intval("" . $this->getFlags());
			$flagValue = $flagValue | $powerOfTwo;
			$this->setFlags($flagValue);
		}
		return $this;
	}
	public function resetFlagAt($pos)
	{
		$powerOfTwo = __object__::getPowerOfTwo($pos);
		if ($powerOfTwo != 0) {
			$flagValue = intval("" . $this->getFlags());
			$powerOfTwo = self::$__ALL32BITS_SET - $powerOfTwo; //This is a 32bits operation 
			$flagValue = $flagValue & $powerOfTwo;
			$this->setFlags($flagValue);
		}
		return $this;
	}
	public function isFlagSetAt($pos)
	{
		$powerOfTwo = __object__::getPowerOfTwo($pos);
		$blnSet = false;
		if ($powerOfTwo != 0) {
			$flagValue = intval("" . $this->getFlags());
			$blnSet = (($flagValue & $powerOfTwo) == $powerOfTwo);
		}
		return $blnSet;
	}
	//Dealing with updates 
	public function addToUpdateList($key, $value)
	{
		//Make it easier to translate to json format in cols
		$index = sizeof($this->l__update);
		$this->l__update[$index] = array();
		$this->l__update[$index][$key] = $value;
	}
	public function addToPropertyUpdateList($key, $value)
	{
		$this->p_l_update[$key] = $value;
	}
	public function getUpdateList()
	{
		return $this->l__update;
	}
	public function clearUpdateList()
	{
		$this->l__update = null;
		$this->l__update = array();
		$this->p_l_update = null;
		$this->p_l_update = array();
	}
	public function update($rollback = true)
	{
		if (sizeof($this->l__update) == 0) throw new Exception("Nothing to update");
		//Check Constraints
		self::evaluateBinaryConstraints($this->p_l_update, $this->getMySystemBinaryConstraints());
		$uArray1 = array();
		$uArray1['query'] = "update";
		$uArray1['table'] = $this->getMyTablename();
		$uArray1['cols'] = $this->l__update;
		$uArray1['where'] = json_decode($this->getIdWhereClause(), true);
		$jresult1 = SQLEngine::execute(json_encode($uArray1), $this->conn, $rollback);
		$jArray1 = json_decode($jresult1, true);
		if (is_null($jArray1)) throw new Exception("Could fetch the status of operation");
		if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
		return $this;
	}
	public function delete($rollback = true)
	{
		$uArray1 = array();
		$uArray1['query'] = "delete";
		$uArray1['table'] = $this->getMyTablename();
		$uArray1['where'] = json_decode($this->getIdWhereClause(), true);
		$jresult1 = SQLEngine::execute(json_encode($uArray1), $this->conn, $rollback);
		$jArray1 = json_decode($jresult1, true);
		if (is_null($jArray1)) throw new Exception("Could fetch the status of operation");
		if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
		return $this;
	}
	public static function getSelectedRecords($conn, $query, $singlerecord = false)
	{
		$stmt = $conn->query($query);
		$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$records) throw new Exception("[ Selected Records ] : Empty Records were found");
		if (sizeof($records) == 0) throw new Exception("[ select ] : Empty records were returned");
		if ($singlerecord && sizeof($records) != 1) throw new Exception("[ select.singlerecord ] : None or Multiple records found");
		return array("column" => $records);
	}
	public static function selectQuery($conn, $classname, $pcolumns, $whereArray = null, $singlerecord = false)
	{
		if (is_null($pcolumns)) throw new Exception(["[ select( $classname ) ] : Submitted Empty Column List"]);
		$tablename = Registry::getTablename($classname);
		if (is_null($tablename)) throw new Exception("[ select( $classname ) ] : Could not extract table information");
		$listOfColumns = array();
		foreach ($pcolumns as $pname) {
			if ($pname == "*") {
				$listOfColumns = array("*");
				break;
			}
			$col = Registry::property2column($classname, $pname);
			if (!is_null($col)) {
				$listOfColumns[sizeof($listOfColumns)] = $col;
			}
		}
		if (sizeof($listOfColumns) == 0) throw new Exception("[ select( $classname ) ] : No column matched");
		$jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildSelect(
			array($tablename),
			$listOfColumns,
			$whereArray
		), $conn);
		if (is_null($jresult1)) throw new Exception("[ select( $classname ) ] : Records could not be returned");
		$jArray1 = json_decode($jresult1, true);
		if (is_null($jArray1['code']) != 0) throw new Exception("[ select( $classname ) ] : " . $jArray1['message']);
		if ($jArray1['count'] == 0) throw new Exception("[ select ( $classname )] : zero records were returned");
		if ($singlerecord && $jArray1['count'] != 1) throw new Exception("[ select( $classname ) ] : Zero or Multiple Records were returned, expected one");
		$tresult = array();
		foreach ($jArray1['rows'] as $row1) {
			$index = sizeof($tresult);
			$tresult[$index] = array();
			foreach ($row1 as $colname => $value) {
				$pname = Registry::column2Property($classname, $colname);
				if (is_null($pname)) continue;
				$tresult[$index][$pname] = $value;
			}
		}
		return array("column" => $jArray1['rows'], "property" => $tresult);
	}
	public static function __load_all_custom_data($conn, $classname, $listOfProperties, $mapArray1 = null, $filter = null, $filterOp = null)
	{
		$columnList = array();
		$t1 = Registry::getId0Columnname($classname);
		if (is_null($t1)) throw new Exception("Primary Column Could not be extracted");
		$columnList[sizeof($columnList)] = $t1;
		foreach ($listOfProperties as $pname) {
			$col = Registry::property2column($classname, $pname);
			if (is_null($col)) continue;
			$columnList[sizeof($columnList)] = $col;
		}
		if (sizeof($columnList) == 0) throw new Exception("Could not collect columns information");
		if (!is_null($filter)) {
			$t1 = array();
			foreach ($filter as $pname => $valueArray1) {
				$col = Registry::property2column($classname, $pname);
				if (is_null($col)) continue;
				$t2 = array();
				foreach ($valueArray1 as $val) {
					/*$index = sizeof($t1);
					$t1[$index] = array();
					$t1[$index][$col] = $val;*/
					$index = sizeof($t2);
					$t2[$index] = array();
					$t2[$index][$col] = $val;
				}
				$t2 = array((JSON2SQL::$__OP_OR) => $t2);
				$t1[sizeof($t1)] = $t2;
			}
			$filter = $t1;
		}
		$t1 = array((JSON2SQL::$__OP_AND) => $filter);
		$t2 = array((JSON2SQL::$__OP_NOT) => array((JSON2SQL::$__OP_OR) => $filter));
		$filter = is_null($filter) ? $filter : (is_null($filterOp) ? $t1 : $t2);
		$records = SQLEngine::execute(SimpleQueryBuilder::buildSelect(array(Registry::getTablename($classname)), $columnList, $filter), $conn);
		$records = json_decode($records, true);
		if (is_null($records)) throw new Exception("Could not decode results");
		if ($records['code'] != 0) throw new Exception($records['message']);
		if ($records['count'] == 0) throw new Exception("Empty records");
		return (self::convertRawSQLDataToTabularData($conn, $classname, $records['rows'], $mapArray1));
	}
	public function loadAllCustomData($listOfProperties, $mapArray1 = null, $filter = null, $filterOp = null)
	{
		/*
		$listOfProperties = array[pname] = array('map' => etc)
		return

		array[index][pname] = value
		*/
		$classname = $this->getMyClassname();
		$conn = $this->conn;
		return (self::__load_all_custom_data($conn, $classname, $listOfProperties, $mapArray1, $filter, $filterOp));
	}
	public function updateList($pcolumns, $default_select_empty_value = null)
	{
		//We need to check if checkboxes were available and not set
		//If previous were set and now we are unsetting , this is a technique 
		$gchkname = self::$__CHECKBOX_NAME;
		if (isset($pcolumns[$gchkname])) {
			foreach ($pcolumns[$gchkname] as $chkname => $chkval) {
				if (!isset($pcolumns[$chkname])) {
					//Now changing from 1 to o
					$pcolumns[$chkname] = $chkval;
				}
			}
		}
		$classname = $this->getMyClassname();
		foreach ($pcolumns as $pname => $val) {
			$col = Registry::property2Column($classname, $pname);
			if (!is_null($col)) {
				if (is_null($val) || (!is_null($default_select_empty_value) && ($val == $default_select_empty_value))) continue;
				//We need to make adjustment if necessary
				$refclass = Registry::getReferenceClass($classname, $pname);
				if ($refclass == "DateAndTime") {
					try {
						//$val = ~DateAndTime~::~convertFromGUIDateFormatToSystemDateAndTimeFormat($val);
						$dt1 = DateAndTime::createDateAndTimeFromGUIDate($val);
						$val = $dt1->getTimestamp();
					} catch (Exception $e) {
					}
				}
				//You need to do proper validation at this point
				$maxLength = Registry::getMaximumLength($classname, $pname);
				if (!(is_null($maxLength) || !(strlen($val) > $maxLength))) throw new Exception("[ $pname ($maxLength) ] : Data Length has exceeded the size");
				$regex = Registry::getRegularExpression($classname, $pname);
				if (!(is_null($regex) || preg_match("/" . $regex['rule'] . "/", $val) === 1)) throw new Exception("[ $pname ] : " . $regex['message']);
				//We need to Add to Update List, if this value is different from the already know
				if ($val != $this->getMyPropertyValue($pname)) $this->addToUpdateList($col, $val);
			}
		}
		//Evaluate constraints
		__data__::evaluateBinaryConstraints($pcolumns, Registry::getSystemBinaryConstraints($classname));
		return $this;
	}
	public static function insert($conn, $classname, $pcolumns, $rollback = true, $default_select_empty_value = null)
	{
		if (is_null($pcolumns)) throw new Exception("[ insert( $classname ) ] : Submitted Empty Column List");
		$tablename = Registry::getTablename($classname);
		if (is_null($tablename)) throw new Exception("[ insert( $classname ) ] : Could not extract table information");
		$listOfColumns = array();
		foreach ($pcolumns as $pname => $val) {
			$val = trim($val);
			$col  = Registry::property2column($classname, $pname);
			if (!is_null($col)) {
				if (is_null($val) || $val == "" || (!is_null($default_select_empty_value) && ($val == $default_select_empty_value))) continue;
				//We need to make adjustment if necessary
				$refclass = Registry::getReferenceClass($classname, $pname);
				if ($refclass == "DateAndTime") {
					try {
						//$val = ~DateAndTime~::~convertFromGUIDateFormatToSystemDateAndTimeFormat($val);
						$dt1 = DateAndTime::createDateAndTimeFromGUIDate($val);
						$val = $dt1->getTimestamp();
					} catch (Exception $e) {
					}
				}
				//You need to do proper validation at this point
				$maxLength = Registry::getMaximumLength($classname, $pname);
				if (!(is_null($maxLength) || !(strlen($val) > $maxLength))) throw new Exception("[ $pname ($maxLength) ] : Data Length has exceeded the size");
				$regex = Registry::getRegularExpression($classname, $pname);
				if (!(is_null($regex) || preg_match("/" . $regex['rule'] . "/", $val) === 1)) throw new Exception("[ $pname ] : " . $regex['message']);
				$listOfColumns[$col] = $val;
			}
		}
		if (sizeof($listOfColumns) == 0) throw new Exception("[ insert( $classname ) ] : Could not extract Columns Informations");
		//Evaluate constraints
		__data__::evaluateBinaryConstraints($pcolumns, Registry::getSystemBinaryConstraints($classname));
		//Now Saving
		$jresult1 = SQLEngine::execute(SimpleQueryBuilder::buildInsert(
			$tablename,
			$listOfColumns
		), $conn, $rollback);
		$jArray1 = json_decode($jresult1, true);
		if (is_null($jArray1)) throw new Exception("[ insert( $classname ) ] : Could not extract data from database");
		if ($jArray1['code'] !== 0) throw new Exception($jArray1['message']);
		return $jArray1['id'];
	}
	public static function evaluateBinaryConstraints($payload, $binaryConstraints)
	{
		if (is_null($payload)) return true;
		if (is_null($binaryConstraints)) return true;
		foreach ($binaryConstraints as $constraint1) {
			$lpname = $constraint1['lpname'];
			$rpname = $constraint1['rpname'];
			$op = $constraint1['op'];
			$errorMessage = $constraint1['error-message'];
			$blnNegate = $constraint1['negate'];
			//Extracting values 
			$lvalue = null;
			if (isset($payload[$lpname])) $lvalue = $payload[$lpname];
			$rvalue = null;
			if (isset($payload[$rpname])) $rvalue = $payload[$rpname];
			if (is_null($lvalue) || is_null($rvalue)) continue;
			//Calculating boolean
			$blnCalculate = $blnNegate;
			switch ($op) {
				case "=":
					$blnCalculate = ($lvalue == $rvalue);
					break;
				case "==":
					$blnCalculate = ($lvalue == $rvalue);
					break;
				case "<":
					$blnCalculate = ($lvalue < $rvalue);
					break;
				case "<=":
					$blnCalculate = ($lvalue <= $rvalue);
					break;
				case ">":
					$blnCalculate = ($lvalue > $rvalue);
					break;
				case ">=":
					$blnCalculate = ($lvalue >= $rvalue);
					break;
				case "!":
					$blnCalculate = ($lvalue != $rvalue);
					break;
				case "!=":
					$blnCalculate = ($lvalue !== $rvalue);
					break;
			}
			//Translation to string should be done here, if needed
			$lvalue = $lvalue;
			$rvalue = $rvalue;
			$errorMessage = "[ ( lvalue, rvalue ) : ( $lvalue , $rvalue ) ] : $errorMessage";
			if (!($blnNegate xor $blnCalculate)) throw new Exception($errorMessage);
		}
		return true;
	}
	public abstract function getId();
	public abstract function getId0();
	public abstract function getIdWhereClause();
	public abstract function getId0WhereClause();
	public abstract function setFlags($flags);
	public abstract function getFlags();
	public abstract function getMyClassname();
	public abstract function getMyTablename();
}
