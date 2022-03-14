<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 14/07/2015
 * Time: 10:57
 */

namespace mineplex\plugin\util;

class UtilMath {

    static function random($num)
    {
        return rand(0, ($num - 1));
    }

    static function randInclusive($num)
    {
        return rand(0, $num);
    }

    static function randBetween($min, $max)
    {
        return rand($min, ($max - 1));
    }

    static function randBetweenInclusive($min, $max)
    {
        return rand($min, $max);
    }

}