<?php 
class System {
    public final static function convertIntegerToStringOfAGivenLength($__data, $__len)	{
		$__data = "".$__data; //toString 
		$tempVal = $__data;
		for ($i=strlen($__data); $i < $__len; $i++) $tempVal = "0".$tempVal;
		return $tempVal;
	}
}
?>