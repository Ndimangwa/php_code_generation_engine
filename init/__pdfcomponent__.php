<?php 
class PDFComponent {
    private function __construct()  {

    }
    public static function getComponentInstance()   {
        return ( new PDFComponent() );
    }
}
?>