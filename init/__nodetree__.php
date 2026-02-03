<?php
class NodeTree	{
	private $doc;
	private $nodeList;
	private $filename;
	private $checksum;
	private $dataHolder;
	static $__IS_CLONE_MODE = "_is_clone_mode_";
	public function putData($key, $val)	{
		if (is_null($this->dataHolder)) $this->dataHolder = array();
		$this->dataHolder[$key] = $val;
	}
	public function getData($key)	{
		$mydata = null;
		if (! is_null($this->dataHolder) && isset($this->dataHolder[$key])) $mydata = $this->dataHolder[$key];
		return $mydata;
	}
	/*This is the replacement of a FileFactory, this allows chained methods 
	All operations should be done to the dom only loading from file and saving only 
	*/
	public static function calculateFileChecksum($filename)	{
		/*
		isaacompletechecksum
		*/
		$doc = new DOMDocument(Object::$xmlVersion);
		$doc->load($filename);
		return self::calculateDOMChecksum($doc);
	}
	public static function calculateDOMChecksum($doc)	{
		$referenceHash = md5(Object::$hashText);
		return self::documentHash($doc, $referenceHash);
	}
	public static function createDOMChecksum($doc)	{
		/*
		INPUT A New DOMDocument, can have checksum or not 
		OUTPUT Return a DOMDocument with a valid checksum , same original DOM 
		*/
		$checksumvalue = self::calculateDOMChecksum($doc);
		$rootElement = $doc->documentElement;
		$checksum = $rootElement->getElementsByTagName('isaacompletechecksum');
		if (intval($checksum->length) == 0)	{
			//Not Existing
			$checksum = $doc->createElement('isaacompletechecksum');
			$checksum->appendChild($doc->createTextNode($checksumvalue));
			$rootElement->appendChild($checksum);
		} else if (intval($checksum->length) == 1)	{
			$checksum->item(0)->nodeValue = $checksumvalue;
		} else {
			Object::shootException("DOM Checksum, Duplicate keys found");
		}
		return $doc;
	}
	public static function getFileChecksum($filename)	{
		/*
		return null or read from isaacompletechecksum
		*/
		if (! file_exists($filename)) return null;
		$doc = new DOMDocument(Object::$xmlVersion);
		$doc->load($filename);
		return self::getDOMChecksum($doc);
	}
	public static function getDOMChecksum($doc)	{
		$checksumvalue = null;
		$checksum = $doc->getElementsByTagName('isaacompletechecksum');
		if (intval($checksum->length) == 1)	{
			//Must be a unique existence 
			$checksumvalue = $checksum->item(0)->nodeValue; 
		}
		return $checksumvalue;
	}
	public static function isDOMIntegrityPassed($doc)	{
		$storedDocumentHashValue = trim(self::getDOMChecksum($doc));
		$calculatedDocumentHashValue = trim(self::calculateDOMChecksum($doc));
		//die("Stored : $storedDocumentHashValue : Calculated : $calculatedDocumentHashValue ");
		return (strcmp($calculatedDocumentHashValue, $storedDocumentHashValue) == 0);
	}
	public static function isFileIntegrityPassed($filename)	{
		/*
		return true or false compare calculated and from get 
		*/
		//Load DOM Once 
		if (! file_exists($filename)) return false;
		$doc = new DOMDocument(Object::$xmlVersion);
		$doc->load($filename);
		return self::isDOMIntegrityPassed($doc);
	}
	private static function documentHash($node1, $referenceHash)	{
		$hashCode = $referenceHash;
		$applyLock = false;
		//Pre Order for Non Binary Tree 
		//Deal with this Node first 
		if ($node1->nodeType == XML_ELEMENT_NODE)	{
			if (ctype_space($node1->tagName))	{
				//retain the value of hashCode, same change not 
			} else if ($node1->tagName == 'isaacompletechecksum')	{
				//Make sure the value of a checksum is not part of the calculation 
				//This is to avoid recursive logic which will not end, logic error avoidance
				$applyLock = true;
			} else	{
				//Other Nodes 
				$val = trim($node1->tagName);
				$hashCode = $hashCode.$val;
				$hashCode=md5($hashCode);
			}
		} else if ($node1->nodeType == XML_TEXT_NODE)	{
			$val = trim($node1->nodeValue);
			$hashCode = $hashCode.$val;
			$hashCode = md5($hashCode);
		}
		//We are done with this Node, Check if child nodes present 
		if (! $applyLock && $node1->hasChildNodes())	{
			foreach ($node1->childNodes as $child1)	{
				$hashCode = self::documentHash($child1, $hashCode);
			}
		}
		return $hashCode;
	}
	private static function convertCustomStringToArray($customString1)	{
		/*
		INPUT 0:nodename1;3:nodename2;*:nodename3 
		OUTPUT array[0][0]=0
				array[0][1]=nodename1 
				array[1][0]=3
				
				so nodename1 is/are a parent of nodename2 which is/are parent of nodename3 
		*/
		$customArray = array();
		$nodeListArray = explode(";", $customString1);
		foreach ($nodeListArray as $aListArray)	{
			//0:nodename1 
			$listsize = sizeof($customArray);
			$customArray[$listsize] = array();
			$splitNodesArray = explode(":", $aListArray);
			if (sizeof($splitNodesArray) != 2) return null;
			$customArray[$listsize][0] = $splitNodesArray[0]; 
			$customArray[$listsize][1] = $splitNodesArray[1]; 
		}
		return $customArray;
	}
	private static function packAllCustomNodes($doc, $nodeCollectionArray, $customArray, $currentArrayPosition)	{
		//Terminating Condition
		if (is_null($customArray)) return null;
		if (($currentArrayPosition + 1)> sizeof($customArray)) return $nodeCollectionArray;
		if (($currentArrayPosition != 0) && (sizeof($nodeCollectionArray) == 0)) return null;
		//Fetch the position and the nodename 
		$pos = trim("".$customArray[$currentArrayPosition][0]);
		$nodename = trim($customArray[$currentArrayPosition][1]);
		//Initially the nodeCollectionArray would be null
		if ($currentArrayPosition == 0)	{
			$nodeCollectionArray = array();
			$tempList = $doc->getElementsByTagName($nodename);
			if ($tempList->length == 0) return null;
			if ($pos == "*")	{
				//All records 
				foreach ($tempList as $alist)	{
					$nodeCollectionArray[sizeof($nodeCollectionArray)] = $alist;
				}
			} else {
				//Specific position 
				$pos = intval($pos);
				if ($pos < intval($tempList->length))	{
					$nodeCollectionArray[sizeof($nodeCollectionArray)] = $tempList->item($pos);
				}
			}
			return self::packAllCustomNodes($doc, $nodeCollectionArray, $customArray, $currentArrayPosition + 1);
		} //end-if-pos-zero
		/*Proceed Now with the list */
		$tempNodeCollection = array();
		foreach ($nodeCollectionArray as $node1)	{
			$tempList = $node1->getElementsByTagName($nodename);
			if ($tempList->length == 0) return null;
			if ($pos == "*")	{
				//All records 
				foreach ($tempList as $alist)	{
					$tempNodeCollection[sizeof($tempNodeCollection)] = $alist;
				}
			} else {
				//Specific position 
				$pos = intval($pos);
				if ($pos < intval($tempList->length))	{
					$tempNodeCollection[sizeof($tempNodeCollection)] = $tempList->item($pos);
				}
			}
		}//end-foreach-anode 
		return self::packAllCustomNodes($doc, $tempNodeCollection, $customArray, $currentArrayPosition + 1);
	}
	public static function findCustomNodes($tree1, $customExpression)	{
		/*
		Input: pos:nodename;pos:nodename.....;pos:nodename
		*/
		$doc = $tree1->getDOM();
		if (is_null($doc)) Object::shootException("DOM has not been initialized");
		$nodeListArray = self::convertCustomStringToArray($customExpression);
		$nodeListArray = self::packAllCustomNodes($doc, null, $nodeListArray, 0); 
		if (sizeof($nodeListArray) == 0) $nodeListArray = null;
		return $nodeListArray;
	}
	public static function loadFromFile($filename)	{
		if (! file_exists($filename)) Object::shootException("File [ $filename ] does not exists");
		$doc = new DOMDocument(Object::$xmlVersion);
		$doc->formatOutput = true;
		$doc->load($filename);
		//if (! NodeTree::isDOMIntegrityPassed($doc)) Object::shootException("DOM did not pass Integrity Check");
		$tree1 = new NodeTree($doc);
		$tree1->setFilename($filename);
		$tree1->setChecksum(md5_file($filename));
		return $tree1;
	}
	public static function createBackupFile($filename)	{
		if (! file_exists($filename)) Object::shootException("File [ $filename ] does not exists");
		if (! copy($filename, $filename.".backup")) Object::shootException("Could not create a backup file");
	}
	//Object  
	public function save()	{
		//We need to create a backup  == Automatically it will check file-existence 
		if (! is_null($this->filename) && file_exists($this->filename)) NodeTree::createBackupFile($this->filename);
		//Updating checksum 
		NodeTree::createDOMChecksum($this->doc);
		//We need to write dom to file 
		if (! $this->doc->save($this->filename)) Object::shootException("Could not save the tree, perhaps permission");
		$this->checksum = md5_file($this->filename);
		return $this->checksum;
	}
	public function cloneMe()	{
		
	}
	public function filter($customExpression)	{
		
	}
	public function addNodeAfter($nodeList, $nodeToAdd)	{
		if (is_null($nodeList) || is_null($nodeToAdd)) Object::shootException("Input Data Not Initialized");
		foreach ($nodeList as $node1)	{
			$parent1 = $node1->parentNode;
			if (! is_null($parent1))	{
				$clonedNode = $nodeToAdd->cloneNode(true);
				$parent1->appendChild($clonedNode);
			}
		}
		return $this;
	}
	public function addNodeBefore($nodeList, $nodeToAdd)	{
		if (is_null($nodeList) || is_null($nodeToAdd)) Object::shootException("Input Data Not Initialized");
		foreach ($nodeList as $node1)	{
			$parent1 = $node1->parentNode;
			if (! is_null($parent1))	{
				$clonedNode = $nodeToAdd->cloneNode(true);
				$parent1->insertBefore($clonedNode, $node1);
			}
		}
		return $this;
	}
	public function appendNode($nodeList, $nodeToAdd)	{
		if (is_null($nodeList) || is_null($nodeToAdd)) Object::shootException("Input Data Not Initialized");
		foreach ($nodeList as $node1)	{
			$clonedNode = $node1->cloneNode(true);
			$node1->appendChild($clonedNode);
		}
		return $this;
	}
	private static function constructNodeBlock($doc, $parentNode1, $blockArray)	{
		//Special Case During Initial time 
		if (is_null($parentNode1) && (sizeof($blockArray) == 1))	{
			foreach ($blockArray as $key => $subData)	{
				$parentNode1 = $doc->createElement($key); //Only First Time 
				if (is_array($subData))	{
					foreach ($subData as $key => $nextData) {
						if (is_array($nextData))	{
							$nodeToAppend1 = self::constructNodeBlock($doc, $doc->createElement($key), $nextData);
							if (is_null($nodeToAppend1)) Object::shootException("Returned Null Node");
							$parentNode1->appendChild($nodeToAppend1);
						} else {
							$middleNode1 = $doc->createElement($key);
							$middleNode1->appendChild($doc->createTextNode($nextData));
							$parentNode1->appendChild($middleNode1);
						}
					}
				} else {
					$parentNode1->appendChild($doc->createTextNode($subData));
				}
			}
		} else if (is_null($parentNode1))	{
			Object::shootException("Mulformed Data Array");
		} else {
			foreach ($blockArray as $key => $subData)	{
				$nextParentNode1 = $doc->createElement($key);
				if (is_array($subData))	{
					$nodeToAppend1 = self::constructNodeBlock($doc, $nextParentNode1, $subData);
					if (is_null($nodeToAppend1)) Object::shootException("Returned Null Node");
					$parentNode1->appendChild($nodeToAppend1);
				} else {
					$nextParentNode1->appendChild($doc->createTextNode($subData));
					$parentNode1->appendChild($nextParentNode1);
				}
			}
		}
		return $parentNode1;
	}
	public function appendNodeBlock($nodeList, $blockArray)	{
		//Create A Node to Be Added First 
		/*
		Input Example of blockArray 
		arr['item']['id'] = 1
					['itemName'] = 'Hello'
					['conference']['conferenceName'] = "NETC"
		*/
		if (is_null($nodeList) || is_null($blockArray)) Object::shootException("Input Data not Initialized");
		$nodeToAppend1 = NodeTree::constructNodeBlock($this->doc, null, $blockArray);
		if (is_null($nodeToAppend1)) Object::shootException("Returned Null Node");
		foreach ($nodeList as $node1)	{
			$clonedNode = $nodeToAppend1->cloneNode(true);
			$node1->appendChild($clonedNode);
		}
		return $this;
	}
	public function replaceNode($nodeList, $nodeToReplace)	{
		if (is_null($nodeList) || is_null($nodeToReplace)) Object::shootException("Collection List or Node to Replace are not Initialized");
		foreach ($nodeList as $node1)	{
			//Clear All Childrens if having 
			if ($node1->hasChildNodes())	{
				foreach ($node1->childNodes as $child1)	{
					$node1->removeChild($child1);
				}
			}
			$clonedNode = $nodeToReplace->cloneNode(true);
			$node1->appendChild($cloneNode);
		}
		return $this;
	}
	public function setNodeContent($nodeList, $content)	{
		if (is_null($nodeList)) Object::shootException("Collection List is not initialized");
		foreach ($nodeList as $node1)	{
			//Clear All Childrens if having 
			if ($node1->hasChildNodes())	{
				foreach ($node1->childNodes as $child1)	{
					$node1->removeChild($child1);
				}
			}
			$node1->appendChild($this->doc->createTextNode($content));
		}
		return $this;
	}
	public function getNodeContent($nodeList)	{
		$content = array();
		if (is_null($nodeList)) Object::shootException("Collection List is not initialized");
		foreach ($nodeList as $node1)	{
			$content[sizeof($content)] = trim($node1->nodeValue);
		}
		if (sizeof($content) == 0) $content = null;
		return $content;
	}
	public function getNodeWithContent($nodeList, $nodeName, $nodeValue)	{
		//nodeList will be your reference place 
		if (is_null($nodeList)) Object::shootException("Collection List is not initialized");
		$foundNode1 = null;
		$breakOuterLoop = false;
		foreach ($nodeList as $node1)	{
			$tempNode1 = new NodeTree($node1);
			$contentValueArr = $tempNode1->getNodeContent(NodeTree::findCustomNodes($tempNode1, "0:$nodeName"));
			if (is_null($contentValueArr)) continue;
			foreach ($contentValueArr as $contentValue)	{
				if (trim($contentValue) == trim($nodeValue))	{
					$foundNode1 = $node1;
					$breakOuterLoop = true;
					break;
				}
			}
			if ($breakOuterLoop) break;
		}
		return $foundNode1;
	}
	public function getNodeListWithValue($nodeList, $value, $pos, $caseSensitive)	{
		/* 
		nodeList :: Our list we are reading from 
		pos: position in our matched list 
		caseSensitive [true/false]
		*/
		if (is_null($nodeList)) return null;
		$pos = trim("".$pos);
		$listOfMatchedNodes = array();
		foreach ($nodeList as $anode1)	{
			//anode1 is doc 
			$nodeValue = $anode1->nodeValue;
			//Lower value equivalent
			$ivalue = strtolower($value);
			$inodeValue = strtolower($nodeValue);
			//Expression B + !A!BC 
			$logic_A = $caseSensitive;
			$logic_B = ($value == $nodeValue);
			$logic_C = ($ivalue == $inodeValue);
			if ($logic_B || (! $logic_A)&& (! $logic_B) && $logic_C)	{
				//Add to matched list 
				$listOfMatchedNodes[sizeof($listOfMatchedNodes)] = $anode1;
			}
		}
		/*
		We need to filter Accoring to the matched Nodes 
		*/
		if ($pos == "*")	{
			//What is matched is Alrite 
		} else {
			$pos = intval($pos);
			if ($pos < sizeof($listOfMatchedNodes))	{
				$tempVal = $listOfMatchedNodes[$pos];
				$listOfMatchedNodes = array();
				$listOfMatchedNodes[0] = $tempVal;
			} else {
				$listOfMatchedNodes = array(); //clear array
			}
		}
		/* Return now the results */
		if (sizeof($listOfMatchedNodes) == 0) $listOfMatchedNodes = null;
		return $listOfMatchedNodes;
	}
	public function getParentNode($nodeList)	{
		//Return first-matched
		if (is_null($nodeList)) Object::shootException("Node List is empty");
		$parentArray = array();
		$parentArray[0] = $nodeList[0]->parentNode;
		return $parentArray;
	}
	public function deleteNode($nodeList)	{
		if (is_null($nodeList)) return $this; //Not Always you have something to delete
		foreach ($nodeList as $node1)	{
			$parent1 = $node1->parentNode;
			if (! is_null($parent1))	{
				$parent1->removeChild($node1);
			}
		}
		return $this;
	}
	public function __construct($doc)	{
		if ($doc == NodeTree::$__IS_CLONE_MODE) return;
		$this->doc = $doc;
	}
	public function setDOM($doc)	{
		$this->doc = $doc;
	}
	public function getDOM()	{ return $this->doc; }
	public function setNodeList($nodeList)	{
		$this->nodeList = $nodeList;
	}
	public function getNodeList()	{ return $this->nodeList; }
	public function setFilename($filename)	{
		$this->filename = $filename;
	}
	public function getFilename()	{ return $this->filename; }
	public function setChecksum($checksum)	{
		$this->checksum = $checksum;
	}
	public function getChecksum()	{ return $this->checksum; }
}
?>