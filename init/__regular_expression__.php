<?php 
class RegularExpression {
    /*
    ^([1][2-9]|[2-9][0-9]|[1][0-9][0-9]|[2][0-1][0-9])(\.[0-9]+)?$
    */
    private static $ruleHolder = array(
        "human-temperature-celsius" => array("rule" => "^(3[4-9]|4[0-2])(\.[0-9]+)?$", "message" => "range of 34.00 to 42.99"),
        "human-respiratory-count" => array("rule" => "^([6-9]|[1-8][0-9]|[9][0-8])(\.[0-9]+)?$", "message" => "range of 6.00 to 98.99"),
        "human-pulse-count" => array("rule" => "^([1][2-9]|[2-9][0-9]|[1][0-9][0-9]|[2][0-1][0-9])(\.[0-9]+)?$", "message" => "range of 12.00 to 219.99"),
        "human-blood-pressure" => array("rule" => "^([1-9][0-9]|[1-2][0-9][0-9]|[3][0-1][0-9])\s*\/\s*([1-9][0-9]|[1-2][0-9][0-9]|[3][0-1][0-9])$", "message" => "example: 120/80 , systolic range 10 to 319.99 and dystolic range 10 to 319.99")
    );
    public static function getRule($rulename)   {
        return (isset(self::$ruleHolder[$rulename]) ? (isset(self::$ruleHolder[$rulename]['rule']) ? ( self::$ruleHolder[$rulename]['rule'] ) : null) : null);
    }
    public static function getMessage($rulename)    {
        return (isset(self::$ruleHolder[$rulename]) ? (isset(self::$ruleHolder[$rulename]['message']) ? ( self::$ruleHolder[$rulename]['message'] ) : null) : null);
    }
}
?>