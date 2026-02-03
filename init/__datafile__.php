<?php 
/*
In HeaderLine, DataLine and DataFile , all object methods which does not return anything must return $this pointer so as to allow method chaining, this is to say , all set-methods must return a $this pointer

Designed and Written by Ndimangwa Fadhili, P.O Box 7436 Moshi, Tanzania. Phone: +255 787 101 808
*/
class LocalException	{
	public static function shootException($message)	{
		throw new Exception($message);
	}
}
class Sort {
	public static function bubbleSort(& $array1, $ascending = true, $shapingFunction = null, $optionArgsArray1 = null)	{
		/*Use bubbleSort techniques in sorting 
		We are passing reference to array, so as to conserve memory, we do not want the system to create another copy 
		We are returning an $array1 as a formality but not necessary 
		$array1 contains data to be sorted , $mode is ascending or descending [if false ]
		$shapingFunction , if not null you need to modifie the columnvalues , like when dealing with objects and we have specific values to compare 
		*/
		$n = sizeof($array1);
		for ($i=0; $i < $n - 1; $i++)	{
			$isAlreadySorted = true;
			for ($j=0; $j < $n - 1 - $i; $j++)	{
				$valuea = $array1[$j];
				$valueb = $array1[$j+1];
				//Shape Whenever necessary
				if (! is_null($shapingFunction) && is_callable($shapingFunction))	{
					$valuea = $shapingFunction($valuea, $array1, $i, $j, $n, $optionArgsArray1);
					if (is_null($valuea)) continue;
					$valueb = $shapingFunction($valueb, $array1, $i, $j, $n, $optionArgsArray1);
					if (is_null($valueb)) continue;
				}
				//Now perform actual sorting 
				if (! $ascending && ($valuea < $valueb)|| $ascending && ($valuea > $valueb))	{
					//swap 
					$t1 = $array1[$j];
					$array1[$j] = $array1[$j+1];
					$array1[$j+1] = $t1;
					$isAlreadySorted = false;
				}
			}
			if ($isAlreadySorted) break; //Efficiency Note:: No need of continuing sorting if we have traversed all elements and we found everything is sorted
		}
		return $array1;
	}
}
class Tool	{
	public static function convertArrayToString($dataArray, $delim=",")	{
		$line = null;
		$count = 0;
		foreach ($dataArray as $dt)	{
			if ($count == 0) $line = $dt;
			else $line .= $delim.$dt;
			$count++;
		}
		return $line;
	}
	public static function getNumberOfAppearanceInArray($arr1, $value)	{
		$count = 0;
		foreach ($arr1 as $tval)	{
			if ($tval == $value) $count++;
		}
		return $count;
	}
	public static function getIndexOfElementInArray($arr1, $value, $index = 0)	{
		$currentIndex = -1;
		$indexcount = -1;
		for ($i=0; $i < sizeof($arr1); $i++)	{
			if ($arr1[$i] == $value)	{
				$indexcount++;
				$currentIndex = $i;
				if ($indexcount == $index) break;
			}
		}
		if (($indexcount == -1) || ($indexcount != $index)) $currentIndex = -1;
		return $currentIndex;
	}
	public static function insertElementInArray($arr1, $value, $index = 0)	{
		$dataArray1 = array();
		$isAlreadyInserted = false;
		for ($i=0; $i < sizeof($arr1); $i++)	{
			if ($i == $index) {
				$dataArray1[sizeof($dataArray1)] = $value;
				$isAlreadyInserted = true;
			}
			$dataArray1[sizeof($dataArray1)] = $arr1[$i];
		}
		//If index not found , insert at the end 
		if (! $isAlreadyInserted) $dataArray1[sizeof($dataArray1)] = $value;
		return $dataArray1;
	}
	public static function removeElementFromArray($arr1, $value, $index = -1)	{
		//Will remove occurance of value specified by index of elements to be removed, 0-based , default -1 remove all occurence 
		$dataArray1 = array();
		$removalCount = 0;
		for ($i=0; $i < sizeof($arr1); $i++)	{
			if (($value == $arr1[$i]) && (($index < 0) || ($removalCount == $index)))	{
				//removal now 
			} else {
				//Keep these
				$dataArray1[sizeof($dataArray1)] = $arr1[$i];
			}
			//The Only Condition to update removalCount is 
			if ($value == $arr1[$i]) $removalCount++;
		}
		return $dataArray1;
	}
	public static function mergeArray($arr1, $arr2)	{
		if (is_null($arr2)) return $arr1;
		//Now arr2 is not null , we need to make sure it is an array 
		if (! is_array($arr2))	{	$t1 = $arr2; $arr2 = array(); $arr2[0] = $t1;}
		if (is_null($arr1)) return $arr2;
		foreach ($arr2 as $dt) $arr1[sizeof($arr1)] = $dt;
		return $arr1;
	}
	public static function correctWidthOfALabel($label, $size)    {
        $len = strlen($label);
        for ($i=$len; $i<$size; $i++) $label = " ".$label;
        return $label;
    }
    public static function progress($index, $total=1, $precision=4, $width=16)   {
        //$index -- current index
        //$total --- last index
        //precision -- decimal precision
        //width -- total width , must be 4 above precision
        if ($precision < 0) $precision = 0;
        if ($width - $precision < 4) $width = $precision + 4;
        if ($total == 0) return 0;
        $label = round(($index * 100) / $total, $precision);
        $label .= "%";
        return self::correctWidthOfALabel($label, $width);
    }
}
class DataFormats {
	public static $_TEXT = 1;
	public static $_ARRAY = 2;
	public static $_JSON = 3;
	public static $_XML = 4;
}
class DataTypes	{
	//Data Types
	public static $_LONG_TEXT = 60; //if len > 255
	public static $_TEXT = 50;
	public static $_DOUBLE = 40;
	public static $_INTEGER = 30;
	public static $_DATE = 20;
	public static $_DATETIME = 10;
	public static $_IGNORE = 0;
	public static $_TOTAL = -1;
	public static $_AVAILABLE_TYPES_COUNT = 8; //Includes Ignore
	public static $_COLNAME = "__type__"; //Used to name Column in a matrix representing COLNAME with DataTypes 
	public static function updateAllDefaltValues($dataMatrix1, $indexcolumnname, $defaultValue = 11)	{
		foreach ($dataMatrix1->getDataLines() as $dataLine1)	{
			$indexcolumnvalue = $dataLine1->getValuesAtColumns($indexcolumnname, 0); // Just 1st record , since it is a loop , if there is another one it will be encoutered later 
			//If multiple headers in a single row, it does not matter, our consern was to get this row indexvalue
			if (is_null($indexcolumnvalue) || is_array($indexcolumnvalue)) continue; //We expect a single value not array, not empty 
			$dataMatrix1 = DataTypes::manageDataMatrix($dataMatrix1, $dataLine1, $indexcolumnname, $indexcolumnvalue, function($storedValue, $currentValue, $currentHeaderColumn, $currentHeaderIndex, $c=null, $d=null, $e=null, $indexcolumnname=null, $indexcolumnvalue=null, $optionArgsArray1 = null)	{
				if ($currentHeaderColumn == $indexcolumnname) return $indexcolumnvalue; //Maintain indexvalues
				return $optionArgsArray1;
			}, $defaultValue);
		}
		return $dataMatrix1;
	}
	public static function manageDataMatrix($dataMatrix1, $dataLine1, $indexcolumnname = null, $indexcolumnvalue = null, $shapingFunction = null, $optionArgsArray1=null)	{
		/*
		Used to update the dataMatrix1 based on the output of shapping function [] Updating will happend at column all $headers of the dataLines
		$dataMatrix1 , the matrix in question , which will be retured
		$indexcolumnname is the index record used to identify which row need to be written 
		$indexcolumnvalue , is the desired value of $indexcolumnname 
		$dataLine1 , the record from your source data which come with a new value , which might substitute the existing one
		$shapingFunction , is a function which will evaluate the original value and the coming value if null , default to storedValue = currentValue
			The shaping function should be able to accept arguments $storedValue, $currentValue, $header=null, $currentHeaderIndex = 0, $storedDataLine1=null, $dataLine1=null, $dataMatrix1=null, $indexcolumnname=null
			
			Example of shapingFunction; 
			function($storedValue, $currentValue, $currentHeaderColumn = null, $currentHeaderIndex = null, $storedDataLine1 = null, $currentDataLine = null, $dataMatrix1 = null, $matchingIndexColumnName = null, $matchingIndexColumnValue=null)	{ 
				//echo "\n\nDATA[ $a $b  ]\n\n";
				return "Calculated";
			};
		$optionArgsArray1 = null
		*/
		if (! is_null($shapingFunction) && ! is_callable($shapingFunction)) return $dataMatrix1;
		$dataLines = $dataMatrix1->filterDatalines($indexcolumnname, $indexcolumnvalue); 
		if (! is_array($dataLines))	{ $t1 = $dataLines; $dataLines = array(); $dataLines[0] = $t1; }
		$originalIndexColumnValue = $indexcolumnvalue; //Incase it is modified in the loop below
		foreach ($dataLines as $storedDataLine1)	{
			$indexcolumnvalue = $originalIndexColumnValue; //restoring the original index column value
			//if we have indexcolumnname set , we need to read indexcolumnvalue for each line 
			if (! is_null($indexcolumnname) && is_null($indexcolumnvalue))	{
				$indexcolumnvalue = $storedDataLine1->getValuesAtColumns($indexcolumnname, 0); // Just 1st record , since it is a loop , if there is another one it will be encoutered later 
				//If multiple headers in a single row, it does not matter, our consern was to get this row indexvalue
				if (is_null($indexcolumnvalue) || is_array($indexcolumnvalue)) $indexcolumnvalue = null; //We expect a single value not array, not empty 
			}
			$alreadyVisitedColumns = array(); //stay with a next header index , incase of already existing header 
			foreach ($dataLine1->getHeaderLine()->getArray() as $header)	{
				if (! isset($alreadyVisitedColumns[$header])) $alreadyVisitedColumns[$header] = 0;
				$currentHeaderIndex = $alreadyVisitedColumns[$header];
				$alreadyVisitedColumns[$header]++;
				$storedValue = $storedDataLine1->getValuesAtColumns($header, $currentHeaderIndex);
				if (is_null($storedValue) || is_array($storedValue)) continue; //We expect a single value not array, not empty 
				$currentValue = $dataLine1->getValuesAtColumns($header, $currentHeaderIndex);
				if (is_null($currentValue) || is_array($currentValue)) continue; //We expect a single value not array, not empty
				if (! is_null($shapingFunction)) $currentValue = $shapingFunction($storedValue, $currentValue, $header, $currentHeaderIndex, $storedDataLine1, $dataLine1, $dataMatrix1, $indexcolumnname, $indexcolumnvalue, $optionArgsArray1);
				if ($currentValue == $storedValue) continue; //We can not update the same value 
				$storedDataLine1->setValuesAtColumns($header, $currentValue, $currentHeaderIndex);
			}
		}
		return $dataMatrix1;
	}
	public static function getTypesMatrixFactory($headerLine1, $defaultValue = 1)	{
		/*
		Will Always return a DataFile indexed with type
		*/
		//clone current headerLine1 since this has been passed as a reference, we do not want to alter it 
		$headerLine1 = $headerLine1->clone();
		//Add column to represent dataType magic word, DataTypes::$_COLNAME
		$headerLine1->insertColumn(DataTypes::$_COLNAME); //default 1st column
		$headerLine1->synchronize(); //The headerLine need to sync before further utilization 
		//We need to create dataLines based on the number of available types 
		$dataLines = array();
		for ($i=0; $i < DataTypes::$_AVAILABLE_TYPES_COUNT; $i++) $dataLines[sizeof($dataLines)] = DataLine::create($headerLine1, $defaultValue, $headerLine1->getNamingShapingFunction(), $headerLine1->getDelimiter());
		$data1 = DataFile::build($headerLine1, $dataLines,  array(DataTypes::$_COLNAME)); //You should index by the __type__ column
		//Modify DataTypes::$_COLNAME field to match the corresponding type 
		//$indicesArray1 = $this->filterDataLinesIndices($indexedcolumnname = "__type__", $indexedcolumnvalue = 1, $rownumber = 0);
		$data1->setValuesAtColumns(DataTypes::$_COLNAME, 1, null, DataTypes::$_LONG_TEXT, 0, 0);
		$data1->setValuesAtColumns(DataTypes::$_COLNAME, 1, null, DataTypes::$_TEXT, 0, 0);
		$data1->setValuesAtColumns(DataTypes::$_COLNAME, 1, null, DataTypes::$_DOUBLE, 0, 0);
		$data1->setValuesAtColumns(DataTypes::$_COLNAME, 1, null, DataTypes::$_INTEGER, 0, 0);
		$data1->setValuesAtColumns(DataTypes::$_COLNAME, 1, null, DataTypes::$_DATE, 0, 0);
		$data1->setValuesAtColumns(DataTypes::$_COLNAME, 1, null, DataTypes::$_DATETIME, 0, 0);
		$data1->setValuesAtColumns(DataTypes::$_COLNAME, 1, null, DataTypes::$_IGNORE, 0, 0);
		$data1->setValuesAtColumns(DataTypes::$_COLNAME, 1, null, DataTypes::$_TOTAL, 0, 0);
		return $data1->synchronize();
	}
	public static function isLongText($data)	{
		return true;
	}
	public static function isText($data)	{
		return (strlen($data) < 255);
	}	
	public static function isDouble($data)	{
		return (preg_match("/^[+-]?([0-9]*[.])?[0-9]+$/", $data)===1);
	}
	public static function isInteger($data)	{
		return (preg_match("/^[+-]?\d+$/", $data) === 1);
	}
	public static function isDate($data)	{
		return false;
	}
	public static function isDateAndTime($data)	{
		return false;
	}
	public static function isIgnoredValue($data, $ignoreValuesArray1)	{
		return (! is_null($ignoreValuesArray1) && in_array($data, $ignoreValuesArray1));
	}
	public static function getDataType($data, $ignoreValuesArray1)	{
		//Test More Specific towards specific, if match any where return 
		$dataType = self::$_LONG_TEXT;
		if (self::isIgnoredValue($data, $ignoreValuesArray1)) return self::$_IGNORE;
		if (self::isDateAndTime($data)) return self::$_DATETIME;
		if (self::isDate($data)) return self::$_DATE;
		if (self::isInteger($data)) return self::$_INTEGER;
		if (self::isDouble($data)) return self::$_DOUBLE;
		if (self::isText($data)) return self::$_TEXT;
		return $dataType;
	}
}
class GeneralLine {
	public function isEmpty()	{
		$isEmpty = true;
		foreach ($this->dataArray as $dt)	{
			$isEmpty = $isEmpty && ($dt == "");
			if (! $isEmpty) break;
		}
		return $isEmpty;
	}
	public function debug($format = 1)	{
		if ($format == DataFormats::$_TEXT)	{
			echo $this->getLine();
		} else if ($format == DataFormats::$_ARRAY)	{
			var_dump($this->getArray());
		} else if ($format == DataFormats::$_JSON)	{
			echo json_encode($this->getArray());
		} else if ($format == DataFormats::$_XML)	{
			echo "XML";
		}
		return $this;
	}
	public function synchronize($dataArray = null)	{ 
		if (is_null($dataArray)) $dataArray = $this->dataArray;
		for ($i=0; $i < sizeof($dataArray); $i++)	{
			if ($i==0) $this->line = $dataArray[$i];
			else $this->line .= $this->delim . $dataArray[$i];
		}
		return $this; 
	}
}
class HeaderLine	extends GeneralLine {
	//Will Have the signature of Heads 
	//In case of similar name you need to index  etc 
	protected $line;
	protected $backupDataArray;
	protected $dataArray;
	protected $delim;
	protected $namingShapingFunction;
	public static $__IS_CLONE_MODE = "is.clone.mode";
	public function clone()	{
		//The good news is in php arrays are default passed as values in arguments
		return ((new HeaderLine(HeaderLine::$__IS_CLONE_MODE))->setLine($this->line)->setBackupArray($this->backupDataArray)->setArray($this->dataArray)->setDelimiter($this->delim)->setNamingShapingFunction($this->namingShapingFunction));
	}
	public function __construct($dataArray, $namingShapingFunction = null, $delim = ',', $optionArgsArray1 = null)	{
		if ($dataArray == HeaderLine::$__IS_CLONE_MODE) return $this;
		$this->delim = $delim;
		$this->backupDataArray = $dataArray; //Save the original 
		$this->dataArray = $dataArray; 
		if (! is_null($namingShapingFunction) && is_callable($namingShapingFunction)) $this->dataArray = $namingShapingFunction($this, $dataArray, $optionArgsArray1);
		//$this->$namingShapingFunction = $namingShapingFunction;
		$this->line = Tool::convertArrayToString($this->dataArray, $delim);
	}
	public function insertColumn($columnname, $index = 0)	{
		$this->dataArray = Tool::insertElementInArray($this->dataArray, $columnname, $index);
		return $this;
	}
	public function removeColumn($columnname, $index = -1)	{
		//Default remove all occurance
		$this->dataArray = Tool::removeElementFromArray($this->dataArray, $columnname, $index);
		return $this;
	}
	public function getIndexOfColumn($header, $index = 0)	{
		return Tool::getIndexOfElementInArray($this->dataArray, $header, $index);
	}
	public function getOccuranceCount($header)	{
		return Tool::getNumberOfAppearanceInArray($this->dataArray, $header);
	}
	public function getIndicesOf($header = null)	{
		//Note ; There is a chances of having headers with the same naming
		if (is_null($header)) return null;
		$indices = null;
		$count = 0;
		foreach ($this->dataArray as $dt)	{
			if ($dt == $header) {
				if (is_null($indices)) $indices = array();
				$indices[sizeof($indices)] = $count;
			}
			$count++;
		}
		return $indices;
	}
	public function setLine($line)	{ $this->line = $line; return $this; }
	public function getLine()	{ return $this->line; }
	private function setBackupArray($bkArray)	{ $this->backupDataArray = $bkArray; return $this; }
	public function getBackupArray()	{ return $this->backupDataArray; }
	private function setArray($dataArray)	{ $this->dataArray = $dataArray; return $this; }
	public function getArray()	{ return $this->dataArray; }
	public function setDelimiter($delim)	{ $this->delim = $delim; return $this; }	
	public function getDelimiter()	{ return $this->delim; }
	public function setNamingShapingFunction($namingShapingFunction) { $this->namingShapingFunction; return $this; }
	public function getNamingShapingFunction()	{ return $this->namingShapingFunction; }
	//synchronize already implemented by super class
}
class DataLine	extends GeneralLine {
	protected $line;
	protected $dataArray;
	protected $delim;
	protected $dataShapingFunction;
	protected $headerLine;
	public static $__IS_CLONE_MODE = "is.clone.mode";
	public static function create($headerLine1, $defaultValue="1", $dataShapingFunction = null, $delim = ',', $optionArgsArray1 = null)	{
		$dataArray = array();
		foreach ($headerLine1->getArray() as $dt)	$dataArray[sizeof($dataArray)] = $defaultValue;
		return (new DataLine($dataArray, $headerLine1, $dataShapingFunction, $delim, $optionArgsArray1));
	}
	public function __construct($dataArray, $headerLine1, $dataShapingFunction = null, $delim = ',', $optionArgsArray1 = null)	{
		if ($dataArray == DataLine::$__IS_CLONE_MODE) return $this;
		$this->headerLine = $headerLine1;
		$headerDataArray1 = $headerLine1->getArray();
		if (sizeof($dataArray) !== sizeof($headerDataArray1)) {
			$t1 = sizeof($dataArray);
			$t2 = sizeof($headerDataArray1);
			/*var_dump($dataArray);
			echo "\n===================\n";
			var_dump($headerDataArray1);*/
			
			throw new Exception("[ data-array( $t1 ) ; header-data-array( $t2 )] : Could not match size of data with size of header");
		}
		$this->delim = $delim;
		$this->dataArray = array();
		foreach ($dataArray as $index => $dt)	{
			//We need to handle columns with the same naming
			$innerIndex = $headerDataArray1[$index];
			if (! isset($this->dataArray[$innerIndex])) $this->dataArray[$innerIndex] = array();
			$this->dataArray[$innerIndex][sizeof($this->dataArray[$innerIndex])] = trim($dt); //Avoid spaces
		}
		//var_dump($this->dataArray);
		//Now we need to shape 
		if (! is_null($dataShapingFunction) && is_callable($dataShapingFunction)) $this->dataArray = $dataShapingFunction($this, $this->dataArray, $optionArgsArray1);
		$this->dataShapingFunction = $dataShapingFunction;
		$this->line = Tool::convertArrayToString($this->getArrayValues(), $delim);
	}
	public function getLine()	{ return $this->line; }
	public function getArray()	{ return $this->dataArray; }
	public function getArrayValues()	{
		//Unmask the complex structure of dataArray to be in a flat array , index versus values 
		$tDataArray = array();
		$tAlreadyVisitedHeaderArray1 = array(); //Track number of times /next indices a header has been visited 
		$listOfHeaders = $this->headerLine->getArray();
		for ($i=0; $i < sizeof($listOfHeaders); $i++)	{
			$header = $listOfHeaders[$i];		
			if (! isset($tAlreadyVisitedHeaderArray1[$header])) {
				$tAlreadyVisitedHeaderArray1[$header] = 0;
			}
			//if (! isset($this->dataArray[$header][$tAlreadyVisitedHeaderArray1[$header]])) throw new Exception("DataLine, Header contain more duplicate headers, than their corresponding data");
			if (! isset($this->dataArray[$header][$tAlreadyVisitedHeaderArray1[$header]])) $tDataArray[$i] = ""; //Empty  there is a new header which has been added
			else $tDataArray[$i] = $this->dataArray[$header][$tAlreadyVisitedHeaderArray1[$header]];
			$tAlreadyVisitedHeaderArray1[$header]++; //Prepare for the next index
		}
		return $tDataArray;
	}
	public function getDelimiter()	{ return $this->delim; }
	public function setDataShapingFunction($dataShapingFunction)	{ $this->dataShapingFunction = $dataShapingFunction; return $this; }
	public function getDataShapingFunction()	{ return $this->dataShapingFunction; }
	public function setHeaderLine($headerLine1)	{ $this->headerLine = $headerLine1; return $this; }
	public function getHeaderLine()	{ return $this->headerLine; }
	public function setValuesAtColumns($column, $value, $index = -1)	{
		//We might values with multipleHeaders
		if (! isset($this->dataArray[$column])) return $this;
		if ($index < 0 || ! isset($this->dataArray[$column][$index]))	{
			//All matched columns 
			foreach ($this->dataArray[$column] as $index => $removethisval)	$this->dataArray[$column][$index] = $value;
			return $this;
		}
		//Now only a specific index left 
		$this->dataArray[$column][$index] = $value;
		return $this;
	}
	public function getValuesAtColumns($column, $index = -1)	{
		//If same name exists , return all or specific value if only 1
		if (! isset($this->dataArray[$column])) return null;
		if ($index < 0 || ! isset($this->dataArray[$column][$index])) return $this->dataArray[$column]; //Return All
		return $this->dataArray[$column][$index];
	}
	public function synchronize($headerLine1 = null)	{ 
		if (! is_null($headerLine1)) {
			//Update HeaderLine
			$this->headerLine = $headerLine1;
			//Update Array Block 
			$headerDataArray1 = $this->headerLine->getArray();
			$dataArray1 = array();
			$alreadyVisitedColumns = array();
			foreach ($headerDataArray1 as $col)	{
				if (in_array($col, $alreadyVisitedColumns)) continue; 
				//Prepare Destination Array
				if (! isset($dataArray1[$col])) $dataArray1[$col] = array();
				//Check from the source Array 
				if (! isset($this->dataArray[$col])) $dataArray1[$col][0] = ""; //Empty
				else foreach ($this->dataArray[$col] as $val) $dataArray1[$col][sizeof($dataArray1[$col])] = $val; //Potential Problem occurs if there exists an added column with the same name as already existed columns , and it was inserted on the left of any one of them
				$alreadyVisitedColumns[sizeof($alreadyVisitedColumns)] = $col;
			}
			$this->dataArray = $dataArray1;
		}
		//Now we need to update line
		parent::synchronize($this->getArrayValues());
		return $this; 
	}
}
class DataFile	{
	private $filename;
	private $lowestHeaderLevel;
	private $delim;
	private $reserveOriginalHeaderIndicesArray1;
	private $headerIndicesArray1; //Note: We might have multiple columns as index [$columnname][$value][$i] = $index
	private $namingShapingFunction;
	private $dataShapingFunction;
	private $headerLine;
	private $dataLines;
	private $dataTypesMatrix;
	private $dataWidthMatrix; 
	private $ignoreValuesArray1;
	private $dataTypePrecision;
	public static $__IS_CLONE_MODE = "__is.clone.mode";
	//Merging Strategy
	public static $_MERGE_ONE_TO_ONE = 1;
	public static $_MERGE_ONE_TO_MANY = 2;
	public static $_MERGE_MANY_TO_MANY = 3;
	public static $_MERGE_MANY_TO_ONE = 4;
	//Reading and Writing to and from DataSources
	public static $_RW_TEXT_CSV = 1;
	public static $_RW_TEXT_JSON = 2;
	public static $_RW_TEXT_XML = 3;
	public static $_RW_DB_MYSQL = 4;
	//General Static Counts
	public static $a_count = 0;
	public static $b_count = 0;
	public static $c_count = 0;
	public static $d_count = 0;
	public static $e_count = 0;
	public static $f_count = 0;
	public static $g_count = 0;
	public function getNumberOfColumns()	{
		return sizeof($this->headerLine->getArray());
	}
	public function getNumberOfCells()	{
		return (sizeof($this->headerLine->getArray()) * sizeof($this->dataLines));
	}
	private function manageDataTypes($currentDataLine1, $indexcolumnname = null)	{
		//This function will modify the $dataTypes and $dataWidth
		if (is_null($this->dataTypesMatrix) || is_null($this->dataWidthMatrix))	{
			$this->dataTypesMatrix = DataTypes::getTypesMatrixFactory($this->headerLine);
			if (! is_null($indexcolumnname)) $this->dataTypesMatrix = DataTypes::updateAllDefaltValues($this->dataTypesMatrix, $indexcolumnname, 0); //Initialize count
			$this->dataWidthMatrix = DataTypes::getTypesMatrixFactory($this->headerLine); // we can also zero it
		}
		$this->dataTypesMatrix = DataTypes::manageDataMatrix($this->dataTypesMatrix, $currentDataLine1, $indexcolumnname, null, function($storedValue, $currentValue, $currentHeaderColumn, $currentHeaderIndex, $storedDataLine1, $dataLine1, $datafile1, $indexcolumnname=null, $indexcolumnvalue=null, $optionArgsArray1 = null)	{
				if ($currentHeaderColumn == $indexcolumnname) return $indexcolumnvalue; //Maintain indexvalues 
				//We need to work with ignoreValuesArray1 first 
				if (! ($indexcolumnvalue == DataTypes::$_TOTAL) && ($indexcolumnvalue == DataTypes::getDataType($currentValue, $optionArgsArray1))) {
					//We need to update total prior return , by adding one too 
					//----------------, we are not counting totals for _IGNORE list 
					if (! ($indexcolumnvalue == DataTypes::$_IGNORE))	{
						$totalValue = $datafile1->getValuesAtColumns($indexcolumnname, DataTypes::$_TOTAL, $currentHeaderColumn, 0, $currentHeaderIndex);
						if (is_null($totalValue) || is_array($totalValue)) $totalValue = 0;
						$totalValue = intval($totalValue) + 1;
						$datafile1->setValuesAtColumns($indexcolumnname, DataTypes::$_TOTAL, $currentHeaderColumn, $totalValue, 0, $currentHeaderIndex);
					}
					return (intval($storedValue) + 1);
				}
				return $storedValue;	//Default Just return the original value
			}, $this->ignoreValuesArray1);
		$this->dataWidthMatrix = DataTypes::manageDataMatrix($this->dataWidthMatrix, $currentDataLine1, $indexcolumnname, null, function($storedValue, $currentValue, $currentHeaderColumn, $currentHeaderIndex, $c=null, $d=null, $e=null, $indexcolumnname=null, $indexcolumnvalue=null, $optionArgsArray1 = null)	{
				if ($currentHeaderColumn == $indexcolumnname) return $indexcolumnvalue; //Maintain indexvalues 
				if (!(DataTypes::getDataType($currentValue, $optionArgsArray1) == DataTypes::$_IGNORE) && (strlen($currentValue) > $storedValue)) $storedValue = strlen($currentValue); //Calculate only if not to be ignored 
				return $storedValue;
			}, $this->ignoreValuesArray1);
		return $this;
	}
	private function manageHeaderIndicesArray($index, $dataLine1, $headerIndicesArray1)	{
		if (is_null($headerIndicesArray1)) return;
		foreach ($headerIndicesArray1 as $header)	{
			$columnvalueArray1 = $dataLine1->getValuesAtColumns($header); 
			if (is_null($columnvalueArray1)) continue; //Not Interested 
			foreach ($columnvalueArray1 as $columnvalue)	{
				if (! isset($this->headerIndicesArray1[$header])) $this->headerIndicesArray1[$header] = array();
				if (! isset($this->headerIndicesArray1[$header][$columnvalue])) $this->headerIndicesArray1[$header][$columnvalue] = array();
				//Now Append, now it will point to all rows pointed by this key 
				$this->headerIndicesArray1[$header][$columnvalue][sizeof($this->headerIndicesArray1[$header][$columnvalue])] = $index;
			}		
		}
		return $this;
	}
	private function buildAssociatedHeaderInformation($headerLine1, $headerIndicesArray1=null, $ignoreValuesArray1=null, $dataTypePrecision=100)	{
		$this->headerLine = $headerLine1;
		if (! is_null($headerIndicesArray1)) $this->headerIndicesArray1 = array();
		$this->reserveOriginalHeaderIndicesArray1 = $headerIndicesArray1; 
		$this->ignoreValuesArray1 = $ignoreValuesArray1;
		$this->dataTypePrecision = $dataTypePrecision;
		$this->dataLines = array();
		$this->dataTypes = DataLine::create($this->headerLine, 1); 
		$this->dataWidth = DataLine::create($this->headerLine, 1);
		$this->namingShapingFunction = $headerLine1->getNamingShapingFunction();
		$this->delim = $headerLine1->getDelimiter();
		$this->lowestHeaderLevel = 0;
		return $this;
	}
	private function buildAssociatedDataLinesInformation($dataLine1, $headerIndicesArray1=null)	{
		//Must be dataLines
		$index = sizeof($this->dataLines);
		$this->dataLines[$index] = $dataLine1;
		//Working with $headerIndicesArray1
		$this->manageHeaderIndicesArray($index, $dataLine1, $headerIndicesArray1);
		//Working with data types
		return $this;
	}
	public static function build($headerLine1=null, $dataLines=null,  $headerIndicesArray1=null, $ignoreValuesArray1=null, $dataTypePrecision=100)	{
		//Things line, delim extract from healderLine1 , will create new csv object from the headerLine1 and dataLines
		//Note if you just call build() , this is the same as $__IS_CLONE_MODE
		$data1 = new DataFile(DataFile::$__IS_CLONE_MODE);
		if (! is_null($headerLine1) && ! is_null($dataLines))	{
			$data1->buildAssociatedHeaderInformation($headerLine1, $headerIndicesArray1, $ignoreValuesArray1, $dataTypePrecision);
			foreach ($dataLines as $dataLine1)	{
				$data1->buildAssociatedDataLinesInformation($dataLine1, $headerIndicesArray1);
			}
			$data1->cleanHeaderIndicesArray();
		}
		return $data1;
	}
	public static function read($mode = 1, $namingShapingFunction = null, $dataShapingFunction = null, $optionArgsArray1 = null)	{
		//Default -- read is static , since we need to read externally to get the datafile
		$mode = DataFile::$_RW_TEXT_CSV;
		$data1 = new DataFile(DataFile::$__IS_CLONE_MODE);
		return $data1;
	}
	public function write($mode = 1, $shapingFunction = null, $optionArgsArray1 = null)	{
		//This object already exists 
		$mode = DataFile::$_RW_TEXT_CSV;
		if ($mode == DataFile::$_RW_TEXT_CSV)	{
			$fout1 = fopen($this->filename, "w") or LocalException::shootException("Failed to open the file for reading");
			$headerLine1 = $this->headerLine->synchronize();
			$lineToWrite = $headerLine1->getLine();  $lineToWrite .= "\n";
			fwrite($fout1, $lineToWrite) or LocalException::shootException("Could not write headers");
			$count = 1;
			foreach ($this->dataLines as $dataLine1)	{
				$count++;
				$dataLine1 = $dataLine1->synchronize($this->headerLine);
				$lineToWrite = $dataLine1->getLine(); $lineToWrite .= "\n";
				fwrite($fout1, $lineToWrite) or LocalException::shootException("Could not write data at line $count");
			}
			fclose($fout1);
		} else {
			//Other Modes
		}
		return $this;
	}
	public function __construct($filename,  $headerIndicesArray1=null, $namingShapingFunction = null, $dataShapingFunction = null, $lowestHeaderLevel=0, $delim = ',', $ignoreValuesArray1=null, $dataTypePrecision=100, $optionArgsArray1=null)	{
		/*
			$ignoreValuesArray1 like ---  will not be considered in determining dataTypes
			$dataTypePrecision the dataType which has the highest percentage occurance, greater than this value will be considered
		*/
		if ($filename == DataFile::$__IS_CLONE_MODE) return $this;
		ini_set('auto_detect_line_endings', true);	//We need to detect line ends as fgetcsv will return everything		
		if (($handle = fopen($filename, "r")) !== false)	{
			$rowcount = -1;
			while (($data = fgetcsv($handle, 0, $delim)) !== false)	{
				$rowcount++;
				if ($rowcount == $lowestHeaderLevel)	{
					//Must be headerLine ... Initialize all variables which will be needed in data processing
					$this->buildAssociatedHeaderInformation(new HeaderLine($data, $namingShapingFunction,$delim, $optionArgsArray1), $headerIndicesArray1, $ignoreValuesArray1, $dataTypePrecision);
				} else if ($rowcount > $lowestHeaderLevel)	{
					$dataLine1 = new DataLine($data, $this->headerLine, $dataShapingFunction,$delim, $optionArgsArray1);
					$this->buildAssociatedDataLinesInformation($dataLine1, $headerIndicesArray1);
					$this->manageDataTypes($dataLine1, DataTypes::$_COLNAME);
				}
			}
			fclose($handle);
			$this->cleanHeaderIndicesArray();
		}
		//Finalize other initializations
		$this->filename = $filename;
		$this->lowestHeaderLevel = $lowestHeaderLevel;
	}
	public function setFilename($filename)	{ $this->filename = $filename; return $this; }
	public function getFilename()	{ return $this->filename; }
	public function getLowestHeaderLevel()	{ return $this->lowestHeaderLevel; }
	public function getDelimiter()	{ return $this->delim; }
	public function setNamingShapingFunction($namingShapingFunction)	{ $this->namingShapingFunction = $namingShapingFunction; return $this; }
	public function getNamingShapingFunction()	{ return $this->namingShapingFunction; }
	public function setDataShapingFunction($dataShapingFunction) { $this->dataShapingFunction = $dataShapingFunction; return $this; }
	public function getDataShapingFunction()	{ return $this->dataShapingFunction; }
	public function setHeaderLine($headerLine1)	{ $this->headerLine = $headerLine1; return $this; }
	public function getHeaderLine()	{ return $this->headerLine; }
	public function setDataLines($dataLines)	{ $this->dataLines = $dataLines; return $this; }
	public function getDataLines()	{ return $this->dataLines; }
	//public function setDataTypes($dataTypes)	{ $this->dataTypes = $dataTypes; return $this; }
	public function getDataTypesMatrix()	{ return $this->dataTypesMatrix; }	
	public function getOriginalIndicesArray()	{ $this->reserveOriginalHeaderIndicesArray1; }
	public function getHeaderIndicesArray()	{ return $this->headerIndicesArray1; }
	public function getDataWidthMatrix()	{ return $this->dataWidthMatrix; }	
	public function getAllDataTypes()	{
		if (is_null($this->dataTypesMatrix)) return null;
		$precision = $this->dataTypePrecision;
		//Create a return dataLine 
		$typeDataLine1 = DataLine::create($this->headerLine, DataTypes::$_TEXT);
		$alreadyVisitedColumns = array();
		foreach ($this->headerLine->getArray() as $header)	{
			if (! isset($alreadyVisitedColumns[$header])) $alreadyVisitedColumns[$header] = 0;
			else $alreadyVisitedColumns[$header]++;
			$total = $this->dataTypesMatrix->getValuesAtColumns(DataTypes::$_COLNAME, DataTypes::$_TOTAL, $header, 0, 0);
			if (is_null($total) || is_array($total) || $total == 0) continue; //Expect single variable and not an array
			$maxcount = 0;
			$maxdatatype = DataTypes::$_TEXT; //if no type will win , then fall to a normal short-text
			foreach ($this->dataTypesMatrix->getDataLines() as $dataLine1)	{
				//We need to make sure , this is not ignore row or total row 
				$valueType = $dataLine1->getValuesAtColumns(DataTypes::$_COLNAME, 0); //1st value if exists 
				if (is_null($valueType)) continue; //Not found 
				if (is_array($valueType)) { $t1 = $valueType; $valueType = $t1[0]; }
				if ($valueType == DataTypes::$_IGNORE || $valueType == DataTypes::$_TOTAL) continue; //The are not involved in calculations 
				$value = $dataLine1->getValuesAtColumns($header, $alreadyVisitedColumns[$header]);
				if (is_null($value)) continue;
				if (is_array($value)) { $t1 = $value; $value = $t1[0]; }
				if ($value > $maxcount)	{
					$maxcount = $value;
					$maxdatatype = $valueType;
				}
			}
			//We need to calculate here if needed 
			if ((($maxcount * 100) / $total) >= $precision)	{
				$typeDataLine1->setValuesAtColumns($header, $maxdatatype, $alreadyVisitedColumns[$header]); 
			}
		}
		return $typeDataLine1->synchronize();
	}
	public function getDataTypesAtColumns($column)	{
		return $this->dataType->getValuesAtColumns($column); //We might have more than one column with the same name -- changeThis
	}
	public function setIgnoreValuesArray($ignoreValuesArray1)	{
		$this->ignoreValuesArray1 = $ignoreValuesArray1;
		return $this;
	}
	public function getIgnoreValuesArray()	{ return $this->$ignoreValuesArray1; }
	public function setDataTypePrecision($dataTypePrecision)	{ $this->dataTypePrecision = $dataTypePrecision; return $this; }
	public function getDataTypePrecision()	{ return $this->dataTypePrecision; }
	public function head($num = 100)	{
		//However we need to return a dataFile object on the furture and use view function to display
		return $this->slice(0, $num);
	}
	public function tail($num = 100)	{
		//However we need to return a dataFile object on the furture and use view function to display
		$startAt = sizeof($this->dataLines) - $num;
		if ($startAt < 0) $startAt = 0;
		return $this->slice($startAt, $num);
	}
	public function slice($startAt = 0, $num = 100)	{
		//However we need to return a dataFile object on the furture and use view function to display
		$dataLines = array();
		$datasize = sizeof($this->dataLines);
		for ($i = $startAt; $i < $datasize && $i < ($startAt + $num); $i++)	{
			$dataLines[sizeof($dataLines)] = $this->dataLines[$i];
		}
		return DataFile::build($this->headerLine, $dataLines,  $this->reserveOriginalHeaderIndicesArray1, $this->ignoreValuesArray1, $this->dataTypePrecision);
	}
	public function filter($header, $value, $index = null)	{
		$dataLines = $this->filterDatalines($header, $value, $index);
		//Assume scenario where index was specified and we expect one record 
		if (! is_array($dataLines)) {
			$t1 = $dataLines;
			$dataLines = array();
			$dataLines[0] = $t1;
		}
		return DataFile::build($this->headerLine, $dataLines,  $this->reserveOriginalHeaderIndicesArray1, $this->ignoreValuesArray1, $this->dataTypePrecision);
	}
	public function view($format = 1)	{
		if (in_array($format, array(DataFormats::$_TEXT, DataFormats::$_JSON)))	{
			$this->headerLine->debug($format);
			foreach ($this->dataLines as $dataLine1)	{
				echo "\n"; $dataLine1->debug($format);
			}
		}
		return $this;
	}
	public function disp($message)	{
		echo $message;
		return $this;
	}
	public function getValuesAtColumns($indexedcolumnname, $indexedcolumnvalue, $columnname = null, $rownumber = null, $colnumber = null)	{
		/*
		WILL READ FROM ANY COLUMN NOT NECESSARY INDEXED, HOWEVER YOU WILL NEED INDEXED INFORMATION TO GET HOLD OF THIS/THESE LINES
		$indexedcolumnname : An indexedcolumn to use 
		$indexedcolumnvalue : An value which will be used to match the row to be picked 
		$columnname : Another column whose value is to be read / writted 
		$rownumber: In case of multiple records found, which one among these, the default is all 
		$colnumber: In some situation, we might have multiple headers, so a row returned might have two values , so which one among the said ones 
		*/
		if (is_null($columnname)) $columnname = $indexedcolumnname;
		$dataLines = null;
		try {
			$dataLines = $this->filterDatalines($indexedcolumnname, $indexedcolumnvalue, $rownumber);
		} catch (Exception $e)	{ $dataLines = null; }
		if (is_null($dataLines)) return;
		if (! is_array($dataLines))	{
			$t1 = $dataLines; $dataLines = array(); $dataLines[0] = $t1;
		}
		if (is_null($colnumber)) $colnumber = -1;
		$list = null;
		foreach ($dataLines as $dataLine1)	{
			$list = Tool::mergeArray($list, $dataLine1->getValuesAtColumns($columnname, $colnumber)); 
		}
		if (is_null($list) || sizeof($list) == 0) $list = null;
		if (sizeof($list) == 1) $list = $list[0];
		return $list;
	}
	private function manageHeaderIndicesArrayUpdation($index, $columnname, $oldvalue, $newvalue, $colnumber = -1)	{
		/*
		$index : index of dataLine in this object 
		$columnname : columnname which if indexed then we need to deal with it 
		$oldvalue: [ May be array or number ]we need to point to exact old value , since this is the current indexed id , as well as if we have multiple indices at this index 
		$newvalue: knew value which will be our new index id ,
		$colnumber ; index in the oldvalue 
		*/
		if (is_null($oldvalue)) return $this; 
		$oldvalue = $list = Tool::mergeArray(null, $oldvalue); //Put in Array if number 
		$oldvalue = $oldvalue[0];
		//echo "\n\n*****[ index = $index, columnname = $columnname, oldvalue = $oldvalue, newvalue = $newvalue, colnumber = $colnumber ]*****\n\n";
		
		if ($oldvalue == $newvalue) return $this; //if same value , do not waste time 
		if (! isset($this->headerIndicesArray1[$columnname])) return $this; //Do Nothing this column is not index 
		//Find the 1st oldvalue whose index is oldvalue 
		if (! isset($this->headerIndicesArray1[$columnname][$oldvalue])) return $this; //However this should not happen
		//Update the new index 
		//One thing to note, if colnumber if >= 0 (means a column is specified), it does not matter which column number , just remove the 1st one 
		//if < 0 , means remove all and that is it , but you just need to add one column 
		// The difference between all and single colnumber is on removing and not during creating new
		//Assume the new id exists somewhere
		if (isset($this->headerIndicesArray1[$columnname][$newvalue]))	{
			$this->headerIndicesArray1[$columnname][$newvalue][sizeof($this->headerIndicesArray1[$columnname][$newvalue])] = $index;
		} else {
			//The newvalue is not in existing indices 
			$this->headerIndicesArray1[$columnname][$newvalue] = array();
			$this->headerIndicesArray1[$columnname][$newvalue][0] = $index;	 //1st one
		}
		//Now you need to remove 1st occurance of the oldvalue if colnumber , otherwise wipe all
		$tArray1 = $this->headerIndicesArray1[$columnname][$oldvalue];
		$this->headerIndicesArray1[$columnname][$oldvalue] = null;
		$this->headerIndicesArray1[$columnname][$oldvalue] = array();
		if ($colnumber >= 0)	{ //Delete One 
			$isAlreadyDeleted = false;
			foreach ($tArray1 as $lindex)	{
				if (! $isAlreadyDeleted && ($lindex == $index))	{
					//Make sure we delete only 1st occurance and live the rest 
					$isAlreadyDeleted = true;
				} else {
					//Proceed building 
					$this->headerIndicesArray1[$columnname][$oldvalue][sizeof($this->headerIndicesArray1[$columnname][$oldvalue])] = $lindex;
				}
			}
		}
		if (sizeof($this->headerIndicesArray1[$columnname][$oldvalue]) == 0) unset($this->headerIndicesArray1[$columnname][$oldvalue]);
		return $this;
	}
	public function setValuesAtColumns($indexedcolumnname, $indexedcolumnvalue, $columnname=null, $valuetowrite="", $rownumber=null, $colnumber=null, $indicesArray1 = null)	{
		/*
		WILL WRITE TO ANY COLUMN NOT NECESSARY INDEXED, HOWEVER YOU WILL NEED INDEXED INFORMATION TO GET HOLD OF THIS/THESE LINES
		$indexedcolumnname : An indexedcolumn to use 
		$indexedcolumnvalue : An value which will be used to match the row to be picked 
		$columnname : Another column whose value is to be read / writted 
		$rownumber: In case of multiple records found, which one among these, the default is all 
		$colnumber: In some situation, we might have multiple headers, so a row returned might have two values , so which one among the said ones 
		$valuetowrite : The value to be writted to $columnname 
		$indicesArray1 : You can pass your own filteredIndicesArray1 instead of waiting this function to pull by itself, remember this part is been 
						called once per each dataLine, so the next call will return less results since the manageHeaderIndicesArrayUpdation will have 
						reduced some of the items in headerIndicesArray1 which the filterDataLinesIndices relies on, this will allow to pass the constant ones
		*/
		if (is_null($columnname)) $columnname = $indexedcolumnname;
		try {
			if (is_null($indicesArray1)) $indicesArray1 = $this->filterDataLinesIndices($indexedcolumnname, $indexedcolumnvalue, $rownumber);
		} catch (Exception $e)	{ $indicesArray1 = null; }
		if (is_null($indicesArray1)) return $this;
		//Now everything is an array 
		if (is_null($colnumber)) $colnumber = -1; //Compatibility at DataLine 
		foreach ($indicesArray1 as $index)	{
			//We need to update $headerIndicesArray1 if the columnname is among the indexcomns, otherwise this is a lost-followup 
			$this->manageHeaderIndicesArrayUpdation($index, $columnname, $this->dataLines[$index]->getValuesAtColumns($columnname, $colnumber), $valuetowrite, $colnumber);
			//We need to set after handling the headerIndicesArray1 , because it needs the previous value to operate properly
			$this->dataLines[$index]->setValuesAtColumns($columnname, $valuetowrite, $colnumber);
		}
		return $this;
	}
	public function filterDataLinesIndices($header, $value, $index = null)	{
		if (is_null($header) || is_null($value)) throw new Exception("Index Column Name or Index Column Value is not set");
		//index null return all, else single one 
		if (! isset($this->headerIndicesArray1[$header])) throw new Exception("Column [ $header ] is not found in the index columns");;
		if (! isset($this->headerIndicesArray1[$header][$value])) throw new Exception("Value [ $value ] is not found in the column [ $header ]");
		if (! is_null($index))	{
			//Meaning Single record
			if (! isset($this->headerIndicesArray1[$header][$value][$index])) throw new Exception("Index [ $index ] does not exists in (Column, Value) = ($header, $value)");
			$index = $this->headerIndicesArray1[$header][$value][$index];
			if (! isset($this->dataLines[$index])) throw new Exception("Algorithm problem, Index [ $index ] is seen in the indexed structure but not in the underlying data");
			$dt = array();
			$dt[0] = $index;
			return $dt;
		}
		//We can just now return all 
		$dataLines = array(); $uniquenessControl = array();
		foreach ($this->headerIndicesArray1[$header][$value] as $index)	{
			if (! isset($this->dataLines[$index])) throw new Exception("Algorithm problem, Index [ $index ] is seen in the indexed structure but not in the underlying data"); //If-Any miss just return
			if (! isset($uniquenessControl[$index])) {
				$uniquenessControl[$index] = $index;
			}
		}
		return $uniquenessControl; //I need only indices , in set method if I need to pull the original dataLines
	}
	public function filterDatalines($header, $value, $index = null)	{
		if (is_null($header) || is_null($value)) return $this->dataLines; //Just return everything
		$indicesArray1 = $this->filterDataLinesIndices($header, $value, $index);
		$dataLines = array();
		foreach ($indicesArray1 as $index)	$dataLines[sizeof($dataLines)] = $this->dataLines[$index];
		if (sizeof($dataLines) == 1) return $dataLines[0];
		return $dataLines;
	}
	public function merge($dataFile1, $strategy=1, $mergeOptionsColumns=null, $showProgress=false)	{
		/*
		$this(a) = $this(a) + $dataFile1(b)
		mergeOptionsColumns[i]	['columna'] = 'columnname in $this'
								['columnb'] = 'columnname in dataFile1'
								['shapingFunction'] = 'function to shape the columnnames' arg is columnn, option ; it will do for each columna and columnb
								['comparisonFunction'] = 'function to compare, arg is columna, columnb; return true or false ; keep those in true
								['valueRetaintionshipFunction'] = function to determine to keep columna or columnb or based on calculation ie md5, arg columna, columnb,option; return any column or calculated value
		
		We can add more parameters in columns used to merge , use function pass as argument to shape the option
		Example, while merging visits , we can also have an eye on the completion time (after formatting), we need also to specify shaping function
		*/
		$strategy = DataFile::$_MERGE_ONE_TO_ONE; //Default since, I could not pass this constant as Argument
		return $this;
	}	
	public function groupBy($columnname = null)	{
		//Aim, value which are the same will be but in adjacent rows 
		if (is_null($columnname)) return $this;
		if (! isset($this->headerIndicesArray1[$columnname])) return $this;
		$dataLines = array();
		$isAlreadyExistingIndices = array(); //If multiple columns bears same name, and have same value , then we avoid duplicate 
		foreach ($this->headerIndicesArray1[$columnname] as $columnvalue => $datablock1)	{
			foreach ($datablock1 as $index => $datalineindex)	{
				if (in_array($datalineindex, $isAlreadyExistingIndices)) continue;
				$newdatalineindex = sizeof($dataLines);
				$dataLines[$newdatalineindex] = $this->dataLines[$datalineindex];
				$this->headerIndicesArray1[$columnname][$columnvalue][$index] = $newdatalineindex;
				$isAlreadyExistingIndices[sizeof($isAlreadyExistingIndices)] = $datalineindex;
			}
		}
		$this->dataLines = $dataLines;
		return $this;
	}
	private function cleanHeaderIndicesArray()	{
		/*
		AIM: Makes sure there is no multiple values of the same header pointing to the same row 
		    Example arr[col1][val1][i] = index and arr[col1][val1][j] = index should be avoided 
			Will Affect $this->headerIndicesArray1 
		*/
		if (is_null($this->headerIndicesArray1)) return $this; //In-case no index
		foreach ($this->headerIndicesArray1 as $columnname => $valueBlock1)	{
			foreach ($valueBlock1 as $columnvalue => $indexBlock1)	{
				$isAlreadyExistingIndices = null;
				$isAlreadyExistingIndices = array();
				$this->headerIndicesArray1[$columnname][$columnvalue] = null;
				$this->headerIndicesArray1[$columnname][$columnvalue] = array();
				foreach ($indexBlock1 as $datalineindex)	{
					if (in_array($datalineindex, $isAlreadyExistingIndices)) continue;
					$this->headerIndicesArray1[$columnname][$columnvalue][sizeof($this->headerIndicesArray1[$columnname][$columnvalue])] = $datalineindex;
					$isAlreadyExistingIndices[sizeof($isAlreadyExistingIndices)] = $datalineindex;
				}
			}
		}
		return $this;
	}
	private function sortHelper($columnArrays1, $dataLines, $showProgress = false, $currentIndex = 0)	{
		/*
		INPUT: 	$columnArrays1 , an array with $arr[$i][0] = $columnname , $arr[i][1] = {true/false} means ascending or descending 
				$dataLines , a subset of dataLines to deal with 
				$showProgress , show progress bar 
				$currentIndex , index to operate in $columnArrays1 , which specify which columns are we using to sort now and which mode 
		OUTPUT: $dataLines At the end of this exercise , headerIndicesArray1 must be recalculated , 
 		*/
		if (! isset($columnArrays1[$currentIndex]) || ! isset($columnArrays1[$currentIndex][0])) return $dataLines; //Exhausted
		$columnname = $columnArrays1[$currentIndex][0];
		$ascending = true; if (isset($columnArrays1[$currentIndex][1]) && is_bool($columnArrays1[$currentIndex][1])) $ascending = $columnArrays1[$currentIndex][1];
		//We will attend this columname even if is not indexed 
		//Call Immediate child 
		$dataLines = $this->sortHelper($columnArrays1, $dataLines, $showProgress, $currentIndex + 1);
		//Sort Leaves 1st then work upward
		$dataLines = Sort::bubbleSort($dataLines, $ascending, function($dataLine1, & $dataLines = null, $currentOuterIndex = 0, $currentInnerIndex = 0, $currentDataSize = null, $columnname = null)	{
			//Just return 1st value of dataLine1 
			$value1 = $dataLine1->getValuesAtColumns($columnname, 0); //1st column appeared here 
			//echo "\n[ $columnname $value1 ]\n";
			if (is_null($value1) || is_array($value1)) return null;
			return $value1;
		}, $columnname);
		return $dataLines;
	}
	public function sort($columnArrays1, $showProgress=false)	{
		//[[col1, true], [col2, ascending=false]], only columns which are in $headerIndicesArray1 , true means ascending 
		if (is_null($columnArrays1)) return $this;
		if (! is_array($columnArrays1)) return $this;
		//We need to restore the original headerList 
		$headerIndicesArray1 = array();
		foreach ($this->headerIndicesArray1 as $columnname => $tempnull) $headerIndicesArray1[sizeof($headerIndicesArray1)] = $columnname;
		return DataFile::build($this->headerLine, $this->sortHelper($columnArrays1, $this->dataLines, $showProgress),  $headerIndicesArray1, $this->ignoreValuesArray1, $this->dataTypePrecision);
	}
	public function synchronize()	{
		//synchronize headerLine 
		$this->headerLine->synchronize();
		//synchronize dataLines 
		foreach ($this->dataLines as $dataLine1) $dataLine1->synchronize($this->headerLine);
		return $this;
	}
	public function insertColumn($columnname, $index = 0)	{
		$this->headerLine->insertColumn($columnname, $index);
		return $this;
	}
	public function removeColumn($columnname, $index = -1)	{
		//Default remove all 
		$this->headerLine->removeColumn($columnname, $index);
		return $this;
	}
	public function columns($columns)	{
		//remove all-columns not found in columns 
		foreach ($this->headerLine->getArray() as $header)	{
			if (! in_array($header, $columns)) $this->removeColumn($header);
		}
		return $this;
	}
	public function getIndexOfColumn($columnname, $index = 0)	{
		$index = $this->headerLine->getIndexOfColumn($columnname, $index);
		if ($index == -1) return null;
		return $index;
	}
	public function getHeaderOccuranceCount($columnname)	{
		return $this->headerLine->getOccuranceCount($columnname);
	}
	public function renameColumn($columnname, $index = -1)	{
		return $this;
	}
	public function categorize($columnname, $indexofcolumnname = 0, $newcolumnname = null, $newcolumnposition = null, $shapingFunction = null, $optionArgsArray1 = null)	{
		/*
		columnname is the name of the columnname whose  value need to be categorized, this function will only do to a single column , not multiple 
		indexofcolumnname , which index of columnname, if columnname exists multiple times , then we can have other index other than zero 
		newcolumnname , the name of the new columnname, if does not exists , add __columnname__
		newcolumnposition , the position where it should be insert, default calculate , just right of the columnname 
		*/
		
		if (is_null($newcolumnname)) $newcolumnname = "__".$columnname."__";
		if (is_null($newcolumnposition))	{
			$newcolumnposition = $this->getIndexOfColumn($columnname, $indexofcolumnname);
			if (is_null($newcolumnposition)) return $this; //We dod not find this header
			$newcolumnposition++; //Adjacent on the right 
		}
		$this->insertColumn($newcolumnname, $newcolumnposition);
		$this->headerLine->synchronize(); //Make sure header is uptodate
		foreach ($this->dataLines as $dataLine1)	{
			$dataLine1->synchronize($this->headerLine); //Make sure toget proper structure 
			if (is_null($shapingFunction) || ! is_callable($shapingFunction)) continue; //Atleast I have synced an empty cell 
			$columnvalue = $dataLine1->getValuesAtColumns($columnname, $indexofcolumnname);
			if (is_null($columnvalue) || is_array($columnvalue)) continue;
			$categorizedvalue = $shapingFunction($this, $columnvalue, $columnname, $indexofcolumnname, $newcolumnname, $newcolumnposition, $dataLine1, $optionArgsArray1);
			$count = $this->getHeaderOccuranceCount($newcolumnname);
			if ($count == 0) throw new Exception("Header [ $newcolumnname ] were not synced properly to dataline");
			$dataLine1->setValuesAtColumns($newcolumnname, $categorizedvalue, $count - 1); //Last index to be added that is the one 
			$dataLine1->synchronize(); //
		}
		return $this;
	}
}
?>
