<?php 
class UIStatus extends UIView {
    public static function __get_general_rounded_status__($borderColor, $contentColor, $size = 8) {
        $status = "<span class=\"border $borderColor rounded-circle $contentColor p-1\">";
        for ($i=0; $i< 8; $i++) {
            $status .= "&nbsp;";
        }
        $status .= "</span>";
        return $status;
    }
    public static function getPrimaryRoundedStatus($size = 8)   {
        return self::__get_general_rounded_status__("border-dark", "bg-primary", $size);
    }
    public static function getDangerRoundedStatus($size = 8)    {
        return self::__get_general_rounded_status__("border-dark", "bg-danger", $size);
    }
    public static function getWarningRoundedStatus($size = 8)   {
        return self::__get_general_rounded_status__("border-dark", "bg-warning", $size);
    }
    public static function getDarkRoundedStatus($size = 8)  {
        return self::__get_general_rounded_status__("border-danger", "bg-dark", $size);
    }
}
?>