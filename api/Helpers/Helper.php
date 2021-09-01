<?php
header("Access-Control-Allow-Origin: *");
class Helper
{

    public static function test_input($string)
    {
        $string = trim($string);
        $string = stripslashes($string);
        $string = htmlspecialchars($string);
        return $string;
    }
}
