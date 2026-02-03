<?php 
class DataConvertorFactory  {
    public static function registrationNumberOnDisplay($value, $optionArgumentArray1 = null)    {
        return $value;
    }
    public static function registrationNumberOnSave($value, $optionArgumentArray1 = null)   {
        return $value;
    }  
    /*__convert is the entry point */
    public static function __convert__($function1, $value, $optionArgumentArray1 = null)    {
        switch ($function1) {
            case "registrationNumberOnDisplay":
                return self::registrationNumberOnDisplay($value, $optionArgumentArray1);
            case "registrationNumberOnSave":
                return self::registrationNumberOnSave($value, $optionArgumentArray1);
        }
    }
}
?>