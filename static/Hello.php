<?php 
class Hello {
    public function addNumbers($a, $b)  {
        return $a+$b;
    }
    private static function helloMin($helloMint)    {
        return "Hello".$helloMint;
    }
    public static function displayHello($arg = "Ndimangwa") {
        return $arg;
    }
}
?>