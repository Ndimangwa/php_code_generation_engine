<?php
/*
Developed by Ndimangwa Fadhili Ngoya
Developed on 15th April, 2021
Phone: +255 787 101 808 / +255 762 357 596
Email: ndimangwa@gmail.com 

32bits engine
Initialization codes
*/
abstract class __object__
{
    //01.Exception Handling 
    protected function throwMe($message)
    {
        self::shootException($message);
    }
    public static function shootException($message)
    {
        throw new Exception($message);
    }
    //02. Mathematical Functions 
    public static function getPowerOfTwo($position)
    {
        $position = intval("" . $position);
        if ($position < 0 || $position > 30) return 0;
        return 1 << $position;
    }
    //--Arrays and Strings
    public static function array2String($array1 /* array('id01' => 'val01', 'id02' => 'val02') */, $excludeValue = null /* ie 'id02' */, $innerdelimiter = ":", $outerdelimiter = ";") {
        //return id01:val01;id02:val02 if id01 were excluded just return id02:val02
        $string1 = null;
        if (is_null($array1)) return null;
        foreach ($array1 as $key => $val)   {
            if (is_null($excludeValue) || ! ($val == $excludeValue))  {
                $t1 = $key . $innerdelimiter . $val;
                $string1 = is_null($string1) ? $t1 : ( $string1 . $outerdelimiter . $t1 );
            }
        }
        return $string1;
    }
    public static function string2Array($string1 /* id01:val01;id02:val02;id03:val03 */, $innerdelimiter = ":", $outerdelimiter = ";")   {
        //return array('id01' => 'val01', 'id02' => 'val02', 'id03' => 'val03')
        if (is_null($string1)) return null;
        $tArray1 = explode($outerdelimiter, $string1);
        $array1 = array();
        foreach ($tArray1 as $tstring1) {
            $tArray2 = explode($innerdelimiter, $tstring1);
            if (sizeof($tArray2) == 2)  {
                $array1[$tArray2[0]] = $tArray2[1];        
            }
        }
        if (sizeof($array1) == 0) $array1 = null;
        return $array1;
    }
    //Extracting values
    public static function getValueFromArray($arrayToLook1, $fieldInArray, $default_value_if_not_found = null)    {
        return ( isset($arrayToLook1[$fieldInArray]) ? $arrayToLook1[$fieldInArray] : $default_value_if_not_found );
    }

    //03. Random Strings
    public static function getRandomMD5Key($salt = null)
    {
        $val = rand(10, 999999);
        if (!is_null($salt)) $val = $val . $salt;
        return md5($val);
    }
    public final static function getMD5CodedString($salt = null, $difficult_level = 32)
    {
        $code = self::getCodeString($difficult_level);
        if (!is_null($salt)) $code = $salt . $code;
        return md5($code);
    }
    public final static function getCodeString($codeLength)
    {
        $lk[1] = "A";
        $lk[2] = "B";
        $lk[3] = "C";
        $lk[4] = "D";
        $lk[5] = "E";
        $lk[6] = "F";
        $lk[7] = "G";
        $lk[8] = "H";
        $lk[9] = "I";
        $lk[10] = "J";
        $lk[11] = "K";
        $lk[12] = "L";
        $lk[13] = "M";
        $lk[14] = "N";
        $lk[15] = "O";
        $lk[16] = "P";
        $lk[17] = "Q";
        $lk[18] = "R";
        $lk[19] = "S";
        $lk[20] = "T";
        $lk[21] = "U";
        $lk[22] = "V";
        $lk[23] = "W";
        $lk[24] = "X";
        $lk[25] = "Y";
        $lk[26] = "Z";
        $lk[27] = "0";
        $lk[28] = "1";
        $lk[29] = "2";
        $lk[30] = "3";
        $lk[31] = "4";
        $lk[32] = "5";
        $lk[33] = "6";
        $lk[34] = "7";
        $lk[35] = "8";
        $lk[36] = "9";
        $lk[37] = "a";
        $lk[38] = "b";
        $lk[39] = "c";
        $lk[40] = "d";
        $lk[41] = "e";
        $lk[42] = "f";
        $lk[43] = "g";
        $lk[44] = "h";
        $lk[45] = "i";
        $lk[46] = "j";
        $lk[47] = "k";
        $lk[48] = "l";
        $lk[49] = "m";
        $lk[50] = "n";
        $lk[51] = "o";
        $lk[52] = "p";
        $lk[53] = "q";
        $lk[54] = "r";
        $lk[55] = "s";
        $lk[56] = "t";
        $lk[57] = "u";
        $lk[58] = "v";
        $lk[59] = "w";
        $lk[60] = "x";
        $lk[61] = "y";
        $lk[62] = "z";
        $codeLength = intval($codeLength);
        $code = "";
        for ($i = 0; $i < $codeLength; $i++) {
            $code = $code . $lk[rand(1, 62)];
        }
        return $code;
    }
    //04. Strings
    public static function property2Caption($propertyName)
    {
        /*
		INPUT nextRegistrationNumber 
		OUTPUT Next Registration Number
		*/
        $propertyArray = preg_split('/(?=[A-Z])/', $propertyName);
        $captionLabel = "";
        foreach ($propertyArray as $val) {
            if ($val != "") {
                $captionLabel .= ucfirst($val) . " ";
            }
        }
        return $captionLabel;
    }
    public static function summarizeString($string, $summaryLength)
    {
        $summaryLength = intval($summaryLength);
        if (strlen($string) > $summaryLength) {
            //We need to process this string 
            $string = substr($string, 0, $summaryLength);
            $string .= "...";
        }
        return $string;
    }
    public static function fixLength($string1, $length, $pad = "0")
    {
        $string1 = "" . $string1;
        for ($i = strlen($string1); $i < $length; $i++)  $string1 = $pad . $string1;
        return $string1;
    }
    public static function inverseArray($array1)
    {
        /*
        1D array ; key => value and value => key
        */
        if (is_null($array1)) return null;
        $newArray1 = array();
        foreach ($array1 as $key => $value) {
            if (!isset($newArray1[$value])) $newArray1[$value] = $key;
        }
        if (sizeof($newArray1) == 0) $newArray1 = null;
        return $newArray1;
    }
    public static function overwriteArray($dataArray1, $overwritingLookupTable = null)
    {
        /*
            replace both key and values based on the overwritingLookupTable 
        */
        if (is_null($overwritingLookupTable)) return $dataArray1;
        $newArray1 = array();
        foreach ($dataArray1 as $key => $bdata) {
            $newkey = isset($overwritingLookupTable[$key]) ? $overwritingLookupTable[$key] : $key;
            if (!is_array($bdata)) {
                $newvalue = isset($overwritingLookupTable[$bdata]) ? $overwritingLookupTable[$bdata] : $bdata;
                $newArray1[$newkey] = $newvalue;
            } else {
                $newArray1[$newkey] = self::overwriteArray($bdata, $overwritingLookupTable);
            }
        }
        return $newArray1;
    }
    //Tools
    public static function getCharacterStream($characterArray1 = array('*'), $length = 80) {
        /*
        Keeps alternating the character in the character array
        */
        $characterArray1 = is_null($characterArray1) ? array('*') : $characterArray1;
        $length = (is_null($length) || $length < 0) ? 80 : $length;
        $stream1 = "";
        $charLength = sizeof($characterArray1);
        if ($charLength == 0) return "";
        for ($i = 0; $i < $length; $i++)    {
            $stream1 .= $characterArray1[$i % $charLength];
        }
        return $stream1;
    }
    //Sorting
    public static function customSortSequenceB($dataArray1, $sortSequenceArray1 = null, $lefttoright = true, $shapingFunction = null)
    {
        if (is_null($shapingFunction)) {
            $shapingFunction = function ($dataArray1, $i) {
                return $dataArray1[$i];
            };
        }
        $tArray1 = $dataArray1;
        if (!is_null($sortSequenceArray1) && sizeof($sortSequenceArray1) > 1) {
            $limit = sizeof($tArray1);
            foreach ($tArray1 as $i => $blk_a) {
                $isAlreadySorted = true;
                $found_b = false;
                $found_b_pos = -1;
                foreach ($tArray1 as $j => $blk_b) {
                    //If you do not manage to sort anything, simple break
                    //We need shapping function so we can get the actual values 
                    $a = $shapingFunction($tArray1, $i);
                    $b = $shapingFunction($tArray1, $j);
                    if ($a == $b) continue;
                    foreach ($sortSequenceArray1 as $cmp) {
                        echo "\n(i = $i), (j = $j), (a = $a), (b = $b), (found_b = $found_b), (found_b_pos = $found_b_pos)\n";
                        if (!$found_b && ($cmp == $b)) {
                            $found_b = true;
                            $found_b_pos = $j;
                            break;
                        } else if ($found_b && ($cmp == $a)) {
                            //Here we are swapping blocks not values
                            $temp = $tArray1[$i]; //or $a
                            $tArray1[$i] = $tArray1[$found_b_pos];
                            $tArray1[$found_b_pos] = $temp;
                            echo "\nNOW BEING SORTED of [$found_b_pos , $j]\n";
                            //Clear if there are other b's and a's in sequence
                            $found_b = false;
                            $found_b_pos = -1;
                            $isAlreadySorted = false;
                            //just break
                            break;
                        }
                    }
                }
                if ($isAlreadySorted) break;
            }
        }
        if (!$lefttoright) {
            $t1 = array();
            for ($i = sizeof($tArray1) - 1; $i >= 0; $i--) {
                $t1[sizeof($t1)] = $tArray1[$i];
            }
            $tArray1 = $t1;
        }
        return $tArray1;
    }
    public static function customSortSequence($dataArray1, $sortSequenceArray1 = null, $lefttoright = true, $shapingFunction = null)
    {
        if (is_null($shapingFunction)) {
            $shapingFunction = function ($dataArray1, $i) {
                return $dataArray1[$i];
            };
        }
        $tArray1 = $dataArray1;
        if (!is_null($sortSequenceArray1) && sizeof($sortSequenceArray1) > 1) {
            $limit = sizeof($tArray1);
            $count_i = -1;
            foreach ($tArray1 as $i => $blk_a) {
                $count_i++;
                if ($count_i == $limit - 1) break;
                $count_j = -1;
                $found_b = false;
                $found_b_pos = -1;
                $found_count_j = -1;
                foreach ($tArray1 as $j => $blk_b) {
                    $count_j++;
                    if ($count_j == $limit) break;
                    if ($count_j <= $count_i) continue;
                    //We need shapping function so we can get the actual values 
                    $a = $shapingFunction($tArray1, $i);
                    $b = $shapingFunction($tArray1, $j);
                    //echo "\n{ $i => $a ; $j => $b }\n";
                    if ($a == $b) continue;
                    //any b should not come before a
                    foreach ($sortSequenceArray1 as $cmp) {
                        if (!$found_b && ($cmp == $b)) {
                            $found_b = true;
                            $found_b_pos = $j;
                            $found_count_j = $count_j;
                        }
                        if ($found_b && ($cmp == $a)) {
                            //The issue here is actually not swapping it is rather an order of listing
                           /* $temp = $tArray1[$i]; //or $a
                            $tArray1[$i] = $tArray1[$found_b_pos];
                            $tArray1[$found_b_pos] = $temp;*/

                            //Re-write $tArray1
                            $newArray1 = array();
                            $found_b = false;
                            $count_k = -1;
                            foreach ($tArray1 as $k => $blk_c)  {
                                $count_k++;
                                if ($count_k == $count_i)   {
                                    $newArray1[$k] = $tArray1[$found_b_pos];
                                } else if ($count_k == $count_j)    {   
                                    $newArray1[$k] = $tArray1[$i];
                                } else {
                                    $newArray1[$k] = $blk_c;
                                }
                            }
                            $tArray1 = $newArray1;
                            //Clear if there are other b's and a's in sequence
                            $found_b = false;
                            $found_b_pos = -1;
                            //just break
                            break;
                        }
                        echo "\n(i = $i), (j = $j), (a = $a), (b = $b), (found_b = $found_b), (found_b_pos = $found_b_pos)\n";
                    }
                }
            }
        }
        if (!$lefttoright) {
            $t1 = array();
            for ($i = sizeof($tArray1) - 1; $i >= 0; $i--) {
                $t1[sizeof($t1)] = $tArray1[$i];
            }
            $tArray1 = $t1;
        }
        return $tArray1;
    }
}
