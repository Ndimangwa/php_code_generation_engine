<?php 
class Collection	{
	//An Advanced form of Array 
	private $list1;
	public function __construct()	{
		$this->list1 = array();
	}
	public function __destruct()	{
		
	}
	public function sortAscending()	{
		$datasize = sizeof($this->list1);
		for ($i = 0; $i < ($datasize - 1); $i++)	{
			$swapped = false;
			for ($j = 0; $j < ($datasize - 1 -$i); $j++)	{
				if (floatval($this->list1[$j]) > floatval($this->list1[$j+1]))	{
					$temp = $this->list1[$j];
					$this->list1[$j] = $this->list1[$j+1];
					$this->list1[$j+1] = $temp;
					$swapped = true;
				}
			}
			if (! $swapeed)	break;
		}
	} 
	public function sortDescending()	{
		$datasize = sizeof($this->list1);
		for ($i = 0; $i < ($datasize - 1); $i++)	{
			$swapped = false;
			for ($j = 0; $j < ($datasize - 1 -$i); $j++)	{
				if (floatval($this->list1[$j+1]) > floatval($this->list1[$j]))	{
					$temp = $this->list1[$j];
					$this->list1[$j] = $this->list1[$j+1];
					$this->list1[$j+1] = $temp;
					$swapped = true;
				}
			}
			if (! $swapeed)	break;
		}
	}
	public function getLength()	{
		return sizeof($this->list1);
	}
	public function isItemInACollection($item)	{
		$found = false;
		$item = trim($item);
		foreach ($this->list1 as $storedItem)	{
			if ($item == trim($storedItem))	{
				$found = true; break;
			}
		}
		return $found;
	}
	public function addCommaSeparatedList($commaList)	{
		//Input 1,2,3....
		$listArr = explode(",", $commaList);
		foreach ($listArr as $item)	{
			$this->list1[sizeof($this->list1)] = trim($item);
		}
		return $this;
	}
	public function addItem($item)	{
		$this->list1[sizeof($this->list1)] = $item;
		return $this;
	}
	public function addItemAt($item, $index)	{
		$this->list1[$index] = $item;
		return $this;
	}
	public function addList($collection1)	{
		if (! is_null($collection1))	{
			foreach ($collection1 as $item)	{
				$this->list1[sizeof($this->list1)] = $item;
			}
		}
		return $this;
	}
	public function addCollection($collection1)	{
		//this is object now 
		return $this->addList($collection1->getCollection());
	}
	public function addObjectToCollection($object1)	{
		if (is_null($object1)) return;
		$this->addItem($object1->getId());
	}
	public function addObjectListToCollection($objectList1)	{
		if (is_null($objectList1)) return;
		foreach ($objectList1 as $obj1)	{
			$this->addItem($obj1->getId());
		}
	}
	public function removeCollection($collectionRemove1)	{
		if (is_null($collectionRemove1)) return;
		$list = array();
		//add the original collection which is not in the remove 
		foreach ($this->list1 as $item)	{
			if (! $collectionRemove1->isItemInACollection($item))	{
				$list[sizeof($list)] = $item;
			}	
		}
		$this->list1 = $list;
		return $this;
	}
	public function getCollection()	{ return $this->list1; }
	public function makeUnique()	{
		$list1 = array();
		foreach ($this->list1 as $item)	{
			$isUnique = true;
			foreach ($list1 as $refItem)	{
				if ($item == $refItem)	{
					$isUnique = false;
					break; //Exit Inner Loop 
				}
			}
			if ($isUnique)	{
				$list1[sizeof($list1)] = $item;
			}
		}
		$this->list1 = $list1;
		return $this;
	}
}
?>