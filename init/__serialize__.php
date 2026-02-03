<?php 
class Serialize {
    private static $serializationTable = array(
        "\"" => "__dkg_alt_dbl__",
        "\'" => "__skg_alt_skl__"
    );
    public static function serializeArray($array1)  {
        return (self::serializeJSON(json_encode($array1)));
    }
    public static function deserializeArray($codedString1)  {
        return json_decode(self::deserializeJSON($codedString1), true);
    }
    public static function serializeJSON($jsonString1)    {
        return self::serializeString($jsonString1);
    }
    public static function deserializeJSON($codedString1)   {
        return self::deserializeString($codedString1);
    }
    public static function serializeString($string1)    {
        foreach (self::$serializationTable as $searchText => $replaceText)  {
            $string1 = str_replace($searchText, $replaceText, $string1);
        }
        return $string1;
    } 
    public static function deserializeString($codedString1) {
        foreach (__object__::inverseArray(self::$serializationTable) as $searchText => $replaceText)    {
            $codedString1 = str_replace($searchText, $replaceText, $codedString1);
        }
        return $codedString1;
    }  
}
?>