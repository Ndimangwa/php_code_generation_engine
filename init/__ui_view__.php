<?php 
class UIView {
    public function __construct()   {}
    public static function wrap($view, $viewclass = "", $contextualfeel = "bg-primary")  {
        return "<div class=\"ui-view $viewclass\"><div class=\"bg-dark p-1\"><div class=\"$contextualfeel p-1\"><div class=\"bg-dark p-1\"><div class=\"bg-white p-1\">$view</div></div></div></div></div>";
    }
}
?>