<?php 
class UIControls extends UIView {
    public function __construct()   {
        parent::__construct();
    }
    public static function getAnchorTag($caption, $mainhref, $optionArgs = null, $optionClasses = null, $optionStyles = null, $optionProperties = null)  {
        $href = $mainhref;
        if (! is_null($optionArgs)) {
           $count = 0;
           foreach ($optionArgs as $key => $value)  {
                $term = $key."=".$value;
                if ($count == 0)    $href .= "?".$term;
                else $href .= "&".$term; 
                $count++;
           }
        }
        //classes
        $myclasses = "";
        if (! is_null($optionClasses))  {
            foreach ($optionClasses as $aclass) {
                $myclasses .= " ".$aclass;
            }
            $myclasses = trim($myclasses);
            $myclasses = "class=\"$myclasses\"";
        }
        $mystyles = "";
        if (! is_null($optionStyles))   {
            foreach ($optionStyles as $astyle)  {
                $optionStyles .= " ".$astyle.";";
            }
            $mystyles = trim($mystyles);
            $mystyles = "style=\"$mystyles\"";
        }
        $myproperties = "";
        if (! is_null($optionProperties))   {
            foreach ($optionProperties as $key => $value)   {
                $t1 = "$key='$value'";
                $myproperties .= " $t1";
            }
        }
        $link1 = "<a href=\"$href\" $myproperties $myclasses $mystyles>$caption</a>";
        return $link1;
    }
}
?>