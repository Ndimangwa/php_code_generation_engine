<?php 
class UICardView extends UIView {
    public function __construct()   {
        parent::__construct();
    }
    private static function customizeBorderHelper($currentData, $borderSettings, $index = 0)    {
        if (is_null($borderSettings)) return $currentData;
        if ($index == sizeof($borderSettings)) return $currentData;
        $currentSettings = $borderSettings[sizeof($borderSettings) - 1 - $index];
        $class = ""; if (isset($currentSettings['class'])) $class = $currentSettings['class'];
        return self::customizeBorderHelper("<div class=\"$class\">$currentData</div>", $borderSettings, $index + 1);
    }
    public static function customize(
        $header = array("text" => "Default Header", "class" => "ui-card-header"),
        $body = array("text" => "Default Body", "class" => "ui-card-body"),
        $footer = array("text" => "Default Footer", "class" => "ui-card-footer"),
        $borderSettings = array(
            array("class" => "bg-dark p-1"),
            array("class" => "bg-primary p-1"),
            array("class" => "bg-dark p-1"),
            array("class" => "bg-white p-1")
        ), $viewClass = "m-1")  {
        /* Each thing is text and class */
        //Bare-Window with no border 
        $bareWindow1 = "<div class=\"card\">";
        if (! is_null($header)) {
            $text = ""; if (isset($header['text'])) $text = $header['text'];
            $class = ""; if (isset($header['class'])) $class = $header['class'];
            $bareWindow1 .= "<div class=\"card-header $class\">$text</div>";
        }
        if (! is_null($body)) {
            $text = ""; if (isset($body['text'])) $text = $body['text'];
            $class = ""; if (isset($body['class'])) $class = $body['class'];
            $bareWindow1 .= "<div class=\"card-body $class\">$text</div>";
        }
        if (! is_null($footer)) {
            $text = ""; if (isset($footer['text'])) $text = $footer['text'];
            $class = ""; if (isset($footer['class'])) $class = $footer['class'];
            $bareWindow1 .= "<div class=\"card-footer $class\">$text</div>";
        }
        $bareWindow1 .= "</div>";
        //Calculating for border
        if (is_null($viewClass)) $viewClass = "";
        $window1 = "<div class=\"ui-view ui-card-view $viewClass\">";
        $window1 .= self::customizeBorderHelper($bareWindow1, $borderSettings, 0);
        $window1 .= "</div>";
        return $window1;
    }
    public static function getSuccesfulReportCard($title, $message, $footer = null, $viewClass = "my-1 p-1") {
        if (! is_null($footer)) $footer = array("text" => $footer, "class" => "text-center text-muted");
        return self::customize(
            array("text" => $title, "class" => "bg-primary text-center"),
            array("text" => $message, "class" => "text-center"),
            $footer,
            array(
                array("class" => "bg-dark p-1"),
                array("class" => "bg-primary p-1"),
                array("class" => "bg-dark p-1"),
                array("class" => "bg-white p-1")
            ), $viewClass
        );
    }
    public static function getDangerReportCard($title, $message, $footer = null, $viewClass = "my-1 p-1") {
        if (! is_null($footer)) $footer = array("text" => $footer, "class" => "text-center text-muted");
        return self::customize(
            array("text" => $title, "class" => "bg-danger text-center"),
            array("text" => $message, "class" => "text-center"),
            $footer,
            array(
                array("class" => "bg-dark p-1"),
                array("class" => "bg-danger p-1"),
                array("class" => "bg-dark p-1"),
                array("class" => "bg-white p-1")
            ), $viewClass
        );
    }
    public static function getDeleteConfirmationReportCard($url, $title, $message = "You are About to delete the item, this is irrevisible action", $buttonText = "Confirm Delete", $footer = null, $viewClass = "my-1 p-1") {
        if (! is_null($footer)) $footer = array("text" => $footer, "class" => "text-center text-muted");
        return self::customizeConfirm(
            $buttonText,
            "btn-danger",
            $url,
            array("text" => $title, "class" => "ui-card-header bg-danger text-white"),
            array("text" => $message, "class" => "ui-card-body text-danger"),
            $footer,
            array(
                array("class" => "bg-danger p-1"),
                array("class" => "bg-dark p-1"),
                array("class" => "bg-danger p-1"),
                array("class" => "bg-white p-1")
            ), "m-1"
        );
    }
    public static function customizeConfirm(
        $buttonText = "Confirm",
        $buttonClasses = "btn-primary",
        $forwardURL = "#",
        $header = array("text" => "Default Header", "class" => "ui-card-header"),
        $body = array("text" => "Default Body", "class" => "ui-card-body"),
        $footer = array("text" => "Default Footer", "class" => "ui-card-footer"),
        $borderSettings = array(
            array("class" => "bg-dark p-1"),
            array("class" => "bg-primary p-1"),
            array("class" => "bg-dark p-1"),
            array("class" => "bg-white p-1")
        ), $viewClass = "m-1")  {
        /* Each thing is text and class */
        //Bare-Window with no border 
        $bareWindow1 = "<div class=\"card\">";
        if (! is_null($header)) {
            $text = ""; if (isset($header['text'])) $text = $header['text'];
            $class = ""; if (isset($header['class'])) $class = $header['class'];
            $bareWindow1 .= "<div class=\"card-header $class\">$text</div>";
        }
        if (! is_null($body)) {
            $text = ""; if (isset($body['text'])) $text = $body['text'];
            $class = ""; if (isset($body['class'])) $class = $body['class'];
            $bareWindow1 .= "<div class=\"card-body $class\"><div class=\"my-2 p-2 text-center\">$text</div><div class=\"text-right pr-2\"><a class=\"btn card-link $buttonClasses\" href=\"$forwardURL\">$buttonText</a></div></div>";
        }
        if (! is_null($footer)) {
            $text = ""; if (isset($footer['text'])) $text = $footer['text'];
            $class = ""; if (isset($footer['class'])) $class = $footer['class'];
            $bareWindow1 .= "<div class=\"card-footer $class\">$text</div>";
        }
        $bareWindow1 .= "</div>";
        //Calculating for border
        if (is_null($viewClass)) $viewClass = "";
        $window1 = "<div class=\"ui-view ui-card-view $viewClass\">";
        $window1 .= self::customizeBorderHelper($bareWindow1, $borderSettings, 0);
        $window1 .= "</div>";
        return $window1;
    }
}
?>
