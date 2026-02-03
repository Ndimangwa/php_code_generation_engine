<?php 
class Service	{
	public final static function getCommaSeparatorFormatFromArray($arrList)	{
		$list = "";
		for ($i = 0; $i < sizeof($arrList); $i++)	{
			if ($i == 0) $list = $arrList[$i];
			else $list .= ", ".$arrList[$i];
		}
		return $list;
	}
}
class Boolean	{
	public final static function lookupForBooleanLetter($index)	{
		$lookuptable = array();
		$lookuptable[0] = "A";$lookuptable[1] = "B";$lookuptable[2] = "C";$lookuptable[3] = "D";$lookuptable[4] = "E";
		$lookuptable[5] = "F";$lookuptable[6] = "G";$lookuptable[7] = "H";$lookuptable[8] = "I";$lookuptable[9] = "J";
		$lookuptable[10] = "K";$lookuptable[11] = "L";$lookuptable[12] = "M";$lookuptable[13] = "N";$lookuptable[14] = "O";
		$lookuptable[15] = "P";$lookuptable[16] = "Q";$lookuptable[17] = "R";$lookuptable[18] = "S";$lookuptable[19] = "T";
		$lookuptable[20] = "U";$lookuptable[21] = "V";$lookuptable[22] = "W";$lookuptable[23] = "X";$lookuptable[24] = "Y";
		$lookuptable[25] = "Z";$lookuptable[26] = "a";$lookuptable[27] = "b";$lookuptable[28] = "c";$lookuptable[29] = "d";
		$lookuptable[30] = "e";$lookuptable[31] = "f";$lookuptable[32] = "g";$lookuptable[33] = "h";$lookuptable[34] = "i";
		$lookuptable[35] = "j";$lookuptable[36] = "k";$lookuptable[37] = "l";$lookuptable[38] = "m";$lookuptable[39] = "n";
		$lookuptable[40] = "o";$lookuptable[41] = "p";$lookuptable[42] = "q";$lookuptable[43] = "r";$lookuptable[44] = "s";
		$lookuptable[45] = "t";$lookuptable[46] = "u";$lookuptable[47] = "v";$lookuptable[48] = "w";$lookuptable[49] = "x";
		$lookuptable[50] = "y";$lookuptable[51] = "z";
		return $lookuptable[$index];
	}
	public final static function getSymbolsList($noOfTerms)	{
		$list = "";
		for ($i = 0; $i < $noOfTerms; $i++)	{
			if ($i == 0) $list = self::lookupForBooleanLetter($i);
			else $list .= ",".self::lookupForBooleanLetter($i);
		}
		return $list;
	}
	public final static function convertBinaryStringToExpression($binaryNumber)	{
		
		$expression = "";
		$binaryNumberArray = str_split($binaryNumber);
		for ($i=0; $i < sizeof($binaryNumberArray); $i++)	{
			$char = $binaryNumberArray[$i];
			$char = trim("".$char);
			$currentLetterToUse = self::lookupForBooleanLetter($i);
			if (strcmp($char, "1") == 0)	{
				$expression .= "#".$currentLetterToUse;
			} else if (strcmp($char, "0") == 0)	{
				$expression .= "!".$currentLetterToUse;
			}
		}
		return $expression;
	}
	public final static function getBitWidth($noOfArguments)	{
		return pow(2, intval($noOfArguments));
	}
	public final static function convertToBinaryArray($number, $bitwidth)	{
		$binaryNumber = self::convertToBinaryString($number, $bitwidth);
		return str_split($binaryNumber);
	}
	public final static function convertToBinaryString($number, $bitwidth)	{
		/*
		if number is 3; and bitwidth = 3; then return 011
		*/
		$number = intval($number);
		$bitwidth = intval($bitwidth);
		$binarybits = "";
		while ($number >= 1)	{
			$remainder = $number % 2;
			$binarybits = $remainder.$binarybits;
			$number = $number / 2;
		}
		//Now Adjust to the Length 
		for ($i=strlen($binarybits); $i < $bitwidth; $i++)	$binarybits = "0".$binarybits;
		//Now return the bits 
		return $binarybits;
	}
	public final static function getNumberOfOnesInABinaryString($binaryNumber)	{
		$binaryNumber = trim("".$binaryNumber);
		$counter = 0;
		foreach (str_split($binaryNumber) as $char)	{
			$char = trim("".$char);
			if (strcmp($char, "1") == 0) $counter++;
		}
		return $counter;
	}
	public final static function isBinaryNumbersDifferByOneBitOnly($number1, $number2)	{
		$number1 =  trim("".$number1); $number2 = trim("".$number2);
		if (strlen($number1) != strlen($number2)) return false;
		$counter = 0;
		$bln = false;
		$numberArr1 = str_split($number1);
		$numberArr2 = str_split($number2);
		for ($i = 0; $i < sizeof($numberArr1); $i++)	{
			if (strcmp($numberArr1[$i], $numberArr2[$i]) != 0) $counter++;
			if ($counter > 1) break; //Have exceeded
		}
		if ($counter == 1) $bln = true;
		return $bln;
	}
	public final static function simplifyTwoNumbersWhichDifferByOneBitOnly($number1, $number2)	{
		$number1 =  trim("".$number1); $number2 = trim("".$number2);
		if (strlen($number1) != strlen($number2)) return $number1;
		$numberArr1 = str_split($number1);
		$numberArr2 = str_split($number2);
		$simplifiedNumber = "";
		for ($i = 0; $i < sizeof($numberArr1); $i++)	{
			if (strcmp($numberArr1[$i], $numberArr2[$i]) != 0)	{
				//Not Equal replace by -
				$simplifiedNumber .= "-";
			} else {
				//Equal rewrite
				$simplifiedNumber .= $numberArr1[$i];
			}
		}
		return $simplifiedNumber;
	}
}
class EssentialPrimeImplicant	{
	public final static function getUnMarkArrayFromTheArray($list1)	{
		$newList = array();
		for ($i=0; $i < sizeof($list1); $i++)	{
			if (strcmp(trim($list1[$i]), "-") != 0)	{
				$newList[sizeof($newList)] = $list1[$i];
			}
		}
		return $newList;
	}
	public final static function markAColumnFromTheListOfColumns($col, $colIndex)	{
		$newCol = array();
		for ($i = 0; $i < sizeof($col); $i++)	{
			$newCol[$i] = "-";
			if ($i != $colIndex)	{
				$newCol[$i] = $col[$i];
			}
		}
		return $newCol;
	}
	public final static function removeAColumnFromTheListOfColumns($col, $colIndex)	{
		$newCol = array();
		for ($i = 0; $i < sizeof($col); $i++)	{
			if ($i != $colIndex)	{
				$newCol[sizeof($newCol)] = $col[$i];
			}
		}
		return $newCol;
	}
	public final static function removeARowFromTheListOfRows($row, $rowIndex)	{
		$newRow = array();
		for ($i = 0; $i < sizeof($row); $i++)	{
			if ($i != $rowIndex)	{
				$newRow[sizeof($newRow)] = $row[$i];
			}
		}
		return $newRow;
	}
	public final static function removeColumsFromTheListOfColumnsDueToMatchedRow($epi, $col, $rowIndex)	{
		$listOfColumns = self::getCorrespondingColumnsFromTheRow($epi, $rowIndex);
		foreach ($listOfColumns as $colIndex)	{
			//These are indices to remove 
			$col = self::markAColumnFromTheListOfColumns($col, $colIndex);
		}
		$col = self::getUnMarkArrayFromTheArray($col);
		return $col;
	}
	public final static function getCorrespondingColumnsFromTheRow($epi, $rowIndex)	{
		$list = array();
		for ($j = 0; $j < sizeof($epi[$rowIndex]); $j++)	{
			if ($epi[$rowIndex][$j])	{
				$list[sizeof($list)] = $j;
			}
		}
		return $list;
	}
	public final static function getCorrespondingRowsFromTheColumn($epi, $columnIndex)	{
		$list = array();
		for ($i = 0; $i < sizeof($epi); $i++)	{
			if ($epi[$i][$columnIndex])	{
				$list[sizeof($list)] = $i;
			}
		}
		return $list;
	}
	public final static function getACorrespondingRowCoveringMaximumColumsForAGivenLeastColumns($epi, $columns, $row_sum, $col_sum)	{
		//Input: epi 2D matrix, columns index of least column , row_sum & col_sum row and column sum respectively 
		//Ouput: a single index for such a row 
		$maxColumnsCovered = 0;
		$selectedRowIndex = 0;
		foreach ($columns as $columnIndex)	{
			$correspondingRows = self::getCorrespondingRowsFromTheColumn($epi, $columnIndex);
			foreach ($correspondingRows as $rowIndex)	{
				$numberOfRowsCoveredByThisRow = $col_sum[$rowIndex];
				if ($numberOfRowsCoveredByThisRow > $maxColumnsCovered)	{
					$maxColumnsCovered = $numberOfRowsCoveredByThisRow;
					$selectedRowIndex = $rowIndex;
				}
			}
		}
		return $selectedRowIndex;
	}
	public final static function getColumnsWithTheLeastSum($row_sum, $col_sum)	{
		//Note: The maximum Sum will be equal to the number of rows 
		//Sum expected from 1 to max number of rows
		$maxNumberOfRows = sizeof($col_sum);
		$columns = array();
		for ($i = 1; ($i <= $maxNumberOfRows) && (sizeof($columns) == 0); $i++)	{	
			for ($j=0; $j < sizeof($row_sum); $j++)	{
				if ($row_sum[$j] == $i)	{
					$columns[sizeof($columns)] = $j;
				}
			}
		}
		return $columns;
	}
	public final static function calculateColumnSum($epi)	{
		//epi 2D array 
		$colSum = array();
		for ($i = 0; $i < sizeof($epi); $i++)	{
			$sum = 0;
			for ($j=0; $j < sizeof($epi[$i]); $j++)	{
				if ($epi[$i][$j]) $sum++;
			}
			$colSum[$i] = $sum;
		}
		return $colSum;
	}
	public final static function calculateRowSum($epi)	{
		//epi 2D array 
		$rowSum = array();
		/*
		Since each row has the same length of column 
		*/
		$noOfColumns = sizeof($epi[0]);
		for ($j = 0; $j < $noOfColumns; $j++)	{
			$sum = 0;
			for ($i=0; $i < sizeof($epi); $i++)	{
				if ($epi[$i][$j]) $sum++;
			}
			$rowSum[$j] = $sum;
		}
		return $rowSum;
	}
	public final static function tabulateContent($epi, $row, $col, $selectedEPIRow)	{
		$table1 = "<table class='yi-sys-display-table'><thead><tr><th></th>";
		foreach ($col as $colIndex)	{
			$table1 .= "<th>$colIndex</th>";
		}
		$table1 .= "<th></th></tr></thead>";
		$row_sum = self::calculateRowSum($epi);
		$col_sum = self::calculateColumnSum($epi);
		$selectedColumns = self::getCorrespondingColumnsFromTheRow($epi, $selectedEPIRow);
		$table1 .= "<tbody>";
		for ($i=0; $i < sizeof($row); $i++)	{
			$rowImplicant1 = $row[$i]; 
			$rowIsSelected = "";
			if ($i == $selectedEPIRow) $rowIsSelected="style='background-color: yellow;'";
			$table1 .= "<tr><td>".Service::getCommaSeparatorFormatFromArray($rowImplicant1->getAllNumberList())."</td>";
			for ($j=0; $j < sizeof($epi[$i]); $j++)	{
				$columnIsSelected="";
				$backupRowIsSelected = $rowIsSelected;
				if (in_array($j, $selectedColumns)) {$rowIsSelected=""; $columnIsSelected="style='background-color: yellow;'";}
				$cellValue = "0";
				if ($epi[$i][$j]) $cellValue = "1";
				$table1 .= "<td $columnIsSelected $rowIsSelected>$cellValue</td>";
				$rowIsSelected = $backupRowIsSelected;
			}
			$table1 .= "<td>".$col_sum[$i]."</td></tr>";
		}
		$table1 .= "<tr><td></td>";
		for ($j=0; $j < sizeof($row_sum); $j++)	{
			$table1 .= "<td>".$row_sum[$j]."</td>";
		}
		$table1 .= "<td></td></tr></tbody>";
		$table1 .= "</table>";
		echo $table1;
	}
	public final static function getEssentialPrimeImplicantHelper($essentialPrimeImplicant1, $row, $col)	{
		if (sizeof($col) == 0) return $essentialPrimeImplicant1; //Terminating Condition 
		if (sizeof($row) == 0) return null; //No enough terms to cover all the minterms
		$epi = array();
		for ($i=0; $i < sizeof($row); $i++)	{
			$rowImplicant1 = $row[$i];
			$listOfMintermsCoveredInARow = $rowImplicant1->getAllNumberList();
			$epi[$i] = array();
			for ($j=0; $j < sizeof($col); $j++)	{
				$epi[$i][$j] = false;
				$minterm = $col[$j]; 
				if (in_array($minterm, $listOfMintermsCoveredInARow)) {
					$epi[$i][$j] = true;
				}
			}
		}
		//Now the epi is set , we need to fill col_sum and row_sum
		$row_sum = self::calculateRowSum($epi);
		$col_sum = self::calculateColumnSum($epi);
		//LowestColumnList in  
		$lowestColumnList = self::getColumnsWithTheLeastSum($row_sum, $col_sum);
		//Get an EPI Row 
		$selectedEPIRow = self::getACorrespondingRowCoveringMaximumColumsForAGivenLeastColumns($epi, $lowestColumnList, $row_sum, $col_sum);
		//Now we have got an EssentialPrimeImplicant 
		$essentialPrimeImplicant1->add($row[$selectedEPIRow]); //Keep this RowImplicant
		//Remove matched columns too 
		$newcol = self::removeColumsFromTheListOfColumnsDueToMatchedRow($epi, $col, $selectedEPIRow);
		$newrow = self::removeARowFromTheListOfRows($row, $selectedEPIRow);
		self::getEssentialPrimeImplicantHelper($essentialPrimeImplicant1, $newrow, $newcol);
	}
	public final static function getEssentialPrimeImplicant($primeImplicant1)	{
		/*
		Input: PrimeImplicant Object 
		Output: array ie #A!B, !C#E etc
		*/
		$row = $primeImplicant1->getAllRowImplicants();
		$col = $primeImplicant1->getAllMinterm();
		$essentialPrimeImplicant1 = new PrimeImplicant(); //Just to Hold the Essential Implicant
		$row_sum = null; /* Stays at the bottom, sum of all rows in each column, driven by row index at a specific column index*/
		$col_sum = null; /* Stays at the right, sum of all columns in each row, driven by column index at a specific row index*/
		self::getEssentialPrimeImplicantHelper($essentialPrimeImplicant1, $row, $col);
		return $essentialPrimeImplicant1;
	}
}
class ImplicantCollection	{
	private $collection1 = null; //Hold array of row Implicants
	public function debug()	{
		foreach ($this->collection1 as $collect1)	{
			$collect1->debug(); echo "<br/>\n";
		}
	}
	public final static function preserveListUniqueness($list1)	{
		$uniqueList1 = array();
		foreach ($list1 as $alist1)	{
			$num = $alist1[0];
			$isminterm = $alist1[1];
			$found = false;
			foreach ($uniqueList1 as $myreflist1)	{
				$refNum = $myreflist1[0];
				if ($num == $refNum)	{
					$found = true;
					break;
				}
			}
			if (! $found)	{
				$listsize = sizeof($uniqueList1);
				$uniqueList1[$listsize] = array();
				$uniqueList1[$listsize][0] = $num;
				$uniqueList1[$listsize][1] = $isminterm;
			}
		}
		return $uniqueList1;
	}
	public final static function mergeRowImplicantList($rowImplicant1, $rowImplicant2)	{
		$list1 = $rowImplicant1->getList();
		$list2 = $rowImplicant2->getList();
		foreach ($list2 as $alist)	{
			$currentlistsize = sizeof($list1);
			$list1[$currentlistsize] = array();
			$list1[$currentlistsize][0] = $alist[0];
			$list1[$currentlistsize][1] = $alist[1];
		}
		$list1 = self::preserveListUniqueness($list1); //Make sure we have a unique list 
		return $list1;
	}
	public final static function combineRowImplicants($collection1, $rowImplicant1, $rowImplicant2)	{
		/*
			Input collection ImplicantCollection
			rowImplicant1 
			rowImplicant2 
		*/
		if (Boolean::isBinaryNumbersDifferByOneBitOnly($rowImplicant1->getBinaryNumber(), $rowImplicant2->getBinaryNumber()))	{
			//We need to construct a new PrimeImplicant and setPrimeImplicant to false, 
			//Also merge all Involved number 
			$newBinaryNumber = Boolean::simplifyTwoNumbersWhichDifferByOneBitOnly($rowImplicant1->getBinaryNumber(), $rowImplicant2->getBinaryNumber());
			$newList = self::mergeRowImplicantList($rowImplicant1, $rowImplicant2);
			$newRowImplicant1 = new RowImplicant($newList, $newBinaryNumber);
			//Automatically primeImplicant1 is true 
			$rowImplicant1->setPrimeImplicant(false);
			$rowImplicant2->setPrimeImplicant(false);
			$collection1->add($newRowImplicant1);
		}
		return $collection1;
	}
	public function __construct()	{
		$this->collection1 = array();
	}
	public function removeDuplicate()	{
		//Terms having 1,4,5 and 4,5,2 should be removed 
		$newCollection1 = array();	
		foreach ($this->collection1 as $collect1)	{
			//BaseLine newCollection1 
			$addRowToCollection = true;
			$list1 = $collect1->getAllNumberList();
			foreach ($newCollection1 as $refCollect1)	{
				$refList1 = $refCollect1->getAllNumberList();
				$listMatched = false;
				if (sizeof($list1) == sizeof($refList1))	{
					$listMatched = true;
					foreach ($list1 as $alist1)	{
						$listMatched = $listMatched && in_array($alist1, $refList1);
						if (! $listMatched) break;
					}
				}
				if ($listMatched) {
					$addRowToCollection = false; //Do not Add this 
					break;
				}
			}
			if ($addRowToCollection)	{
				$newCollection1[sizeof($newCollection1)] = $collect1;
			}
		}
		$this->collection1 = $newCollection1;
	}
	public function add($rowImplicant1)	{
		$this->collection1[sizeof($this->collection1)] = $rowImplicant1;
	}
	public function getAllRowImplicants()	{
		return $this->collection1;
	}
	public function getRowImplicantsBelongingToGroupOfOnes($numberOfOnes)	{
		$list1 = array();
		foreach ($this->collection1 as $acollect1)	{
			if (intval($numberOfOnes) == intval($acollect1->getNumberOfBitsSet()))	{
				$list1[sizeof($list1)] = $acollect1;
			}
		}	
		return $list1;
	}
	public function getHighestGroupOfOnes()	{
		$highest = 0;
		foreach ($this->collection1 as $acollect1)	{
			if (intval($acollect1->getNumberOfBitsSet()) > $highest)	$highest = $acollect1->getNumberOfBitsSet();
		}
		return $highest;
	}
	public function isListTotalPrimeImplicant()	{
		$bln = true;
		foreach ($this->collection1 as $collect1)	{
			$bln = $bln && $collect1->isPrimeImplicant();
			if (! $bln) break;
		}
		return $bln;
	}
	public function getAllPrimeImplicants()	{
		$list = array();
		foreach ($this->collection1 as $collect1)	{
			if ($collect1->isPrimeImplicant())	{
				$list[sizeof($list)] = $collect1;
			}
		}
		return $list;
	}
	public function getAllNonPrimeImplicants()	{
		$list = array();
		foreach ($this->collection1 as $collect1)	{
			if (! $collect1->isPrimeImplicant())	{
				$list[sizeof($list)] = $collect1;
			}
		}
		return $list;
	}
	public function isEmpty()	{
		return (sizeof($this->collection1) == 0);
	}
	public function getAllMinterm()	{
		$list = array();
		foreach ($this->collection1 as $collect1)	{
			$tminterms = $collect1->getAllMinterm();
			foreach ($tminterms as $aterm)	{
				if (! in_array($aterm, $list))	{
					$list[sizeof($list)] = $aterm;
				}
			}
		}
		return $list;
	}
}
class PrimeImplicant extends ImplicantCollection	{
	public function __construct()	{
		parent::__construct();
	}
}
class BooleanSimplifier extends ImplicantCollection	{
	private static function simplifyHelper($primeImplicant1, $booleanSimplifier1)	{
		$simplifier1 = new BooleanSimplifier();
		//Highest GroupNumber 
		$highestNumberOfOnes = $booleanSimplifier1->getHighestGroupOfOnes();
		for ($i=0; $i < $highestNumberOfOnes; $i++)	{
			$rowList_i = $booleanSimplifier1->getRowImplicantsBelongingToGroupOfOnes($i);
			$rowList_i_plusone = $booleanSimplifier1->getRowImplicantsBelongingToGroupOfOnes($i + 1);
			$tempBS = new BooleanSimplifier(); //Only for this row_i and row_i_plusone 
			foreach ($rowList_i as $rowImplicant1)	{
				foreach ($rowList_i_plusone as $rowImplicant2)	{
					//The tempBs is not modified , this is accumulative mode
					$tempBS = ImplicantCollection::combineRowImplicants($tempBS, $rowImplicant1, $rowImplicant2);
				}
			}
			$tCollectionList = $tempBS->getAllRowImplicants();
			foreach ($tCollectionList as $tcollect1)	{
				$simplifier1->add($tcollect1); //These are the new collections only
			}
		}//end-for
		/*
		Now All rowCollection in the original Collection which are stillPrime add them to primeImplicant 
		*/
		$tCollectionList = $booleanSimplifier1->getAllPrimeImplicants();
		foreach ($tCollectionList as $tcollect1)	{
			$primeImplicant1->add($tcollect1);
		}
		//EXIT--POINT 
		//Step Two Take a Recursive approach , we need to define an 
		//Exit Point 
		if (($booleanSimplifier1->isListTotalPrimeImplicant()) && ($simplifier1->isEmpty())) {
			return $primeImplicant1; //Exit Point
		}
		//Else loop 
		self::simplifyHelper($primeImplicant1, $simplifier1);
	}
	public static function simplify($truthTableRows, $numberOfVariables)	{
		/*
		INPUT truthTableRows[i][0] = num i.e 14 
			  truthTableRows[i][1] = true/false isminterm or do not care 
			  numberOfVariables ie 4 
			  return siplified expression array
			  i.e #A!B , !A#B , TAKE # as positive, ! as negation 
		*/
		//To Hold PrimeImplicant
		$primeImplicant1 = new PrimeImplicant();
		//Boolean Simplifier a kind of collection rough paper 
		$simplifier1 = new BooleanSimplifier();
		//Step One Initialize the simplifier 
		foreach ($truthTableRows as $arow)	{
			//Preparing Arguments 
			$list1 = array();
			$list1[0] = array();
			$list1[0][0] = $arow[0];
			$list1[0][1] = $arow[1];
			
			$binaryString = Boolean::convertToBinaryString($arow[0], $numberOfVariables);
			$rowImplicant1 = new RowImplicant($list1, $binaryString);
			//Add to simplifier 
			$simplifier1->add($rowImplicant1);
		}//end-for-each
		self::simplifyHelper($primeImplicant1, $simplifier1);
		//Remove Duplicate ie 1,2,5 and 5,1,2
		$primeImplicant1->removeDuplicate();
		//Now we have our primeImplicant table
		//Now we generate the EssentialPrimeImplicant 
		$essentialPrimeImplicant1 = EssentialPrimeImplicant::getEssentialPrimeImplicant($primeImplicant1); //RowImplicant which are valid 
		//We need to prepare a return minterms
		// in the form #A!B, !C#D
		$expression = array();
		if (! is_null($essentialPrimeImplicant1))	{
			$rowImplicants = $essentialPrimeImplicant1->getAllPrimeImplicants();
			foreach ($rowImplicants as $implicant1)	{
				$expression[sizeof($expression)] = $implicant1->getBooleanExpression();
			}
		}
		return $expression;
	}
	public function __construct()	{
		parent::__construct();
	}
}
class RowImplicant	{
	/*
		DS 
	ArrayRow[i]
		1. ArrayRow[0] = [[num, true], [num, false]......,[number, isminterm]] 
		2. ArrayRow[1] = String of the Binary Number "01-00-100"
		3. ArrayRow[2] = Number of 1s in group 
		5. ArrayRow[3] = Is Prime Implicant [true/false] , default true, by default every term is a prime Implicant 
	*/
	private $ds1 = null;
	public function debug()	{
		var_dump($this->ds1);
	}
	public function __construct($list1, $binaryStringNumber)	{
		/*
		list[i][0] = num; list[i][1] = isminterm ; bitwidth = 2 power numberOfArgument
		The binaryStringNumber , 111000-1
		*/
		$this->ds1 = array();
		$this->setList($list1);
		$this->setBinaryNumber($binaryStringNumber);
		$this->setPrimeImplicant(true);
	}
	public function setList($list1)	{
		$this->ds1[0] = array();
		for($i=0; $i < sizeof($list1); $i++)	{
			$this->ds1[0][$i] = array();
			$this->ds1[0][$i][0] = $list1[$i][0]; //Holds Number ie 3
			$this->ds1[0][$i][1] = $list1[$i][1]; //Holds minterm or do not care true or false respectively
		}
	}
	public function getList()	{
		return $this->ds1[0];
	}
	public function setPrimeImplicant($bln)	{
		$this->ds1[3] = $bln;
	}
	public function isPrimeImplicant()	{ return $this->ds1[3]; }
	public function getNumberOfBitsSet()	{ return $this->ds1[2]; }
	public function getBinaryNumber()	{ return $this->ds1[1]; }
	public function setBinaryNumber($binaryStringNumber)	{
		$this->ds1[1] = $binaryStringNumber;
		$this->ds1[2] = Boolean::getNumberOfOnesInABinaryString($binaryStringNumber);
	}
	public function getBooleanExpression()	{
		return Boolean::convertBinaryStringToExpression($this->getBinaryNumber());
	}
	public function addNumberInAList($list1)	{
		//$list[i][0] = num, $list[i][1] , isminterm 
		foreach ($list1 as $aNewTerm)	{
			$found = false;
			foreach ($this->ds1[0] as $anExistingTerm)	{
				if (intval($aNewTerm[0]) == intval($anExistingTerm[0]))	{
					$found = true; //cannot add This already do exists 
					break; //break to the outer loop 
				}
			}
			if (! $found)	{
				$listsize = sizeof($this->ds1[0]);
				$this->ds1[0][$listsize] = array();
				$this->ds1[0][$listsize][0] = $aNewTerm[0];
				$this->ds1[0][$listsize][1] = $aNewTerm[1];
			}
		}
	}
	public function getAllNumberList()	{
		$list1 = array();
		foreach ($this->ds1[0] as $aterm)	{
			$list1[sizeof($list1)] = $aterm[0];
		}
		return $list1;
	}
	public function getAllMinterm()	{
		$list1 = array();
		foreach ($this->ds1[0] as $aterm)	{
			if ($aterm[1])	{
				$list1[sizeof($list1)] = $aterm[0];
			}
		}
		return $list1;
	}
	public function getAllDoNotCareTerms()	{
		$list1 = array();
		foreach ($this->ds1[0] as $aterm)	{
			if (! $aterm[1])	{
				$list1[sizeof($list1)] = $aterm[0];
			}
		}
		return $list1;
	}
}
?>