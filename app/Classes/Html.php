<?php
namespace App\Classes;

class Html {
    public static function strip($string, $allow = '')
    {
        return htmlspecialchars_decode(strip_tags($string, $allow));
    }
}
