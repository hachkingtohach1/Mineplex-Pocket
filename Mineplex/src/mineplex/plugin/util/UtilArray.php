<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/6/2015
 * Time: 1:07 AM
 */

namespace mineplex\plugin\util;


class UtilArray {

    public static function hasKey($key, array $array)
    {
        return (isset($array[$key]) || array_key_exists($key, $array));
    }

    public static function arrayDiff(array $array, array $subtract)
    {
        //THIS IS TEMP FIX LATER!!
        $return = [];
        foreach ($array as $value)
        {
            if (!in_array($value, $subtract))
                array_push($return, $value);
        }

        return $return;

        //return array_udiff($array, $subtract, ['mineplex\plugin\util\UtilArray', 'comp']);
    }


    static function comp($a,$b)
    {
        if ($a===$b)
        {
            return 0;
        }
        return ($a>$b)?1:-1;
    }

    public static function getValuesRecursively($object)
    {
        if (!is_array($object))
            return [$object];

        $returnArray = [];
        foreach ($object as $value)
        {
            if (is_array($value))
            {
                $returnArray = array_merge($returnArray, array_values(self::getValuesRecursively($value)));
            }
            else
            {
                array_push($returnArray, $value);
            }
        }
        return $returnArray;
    }

}
/*
$obj1 = new \stdClass();
$a1=array("a"=>$obj1,"b"=>"green","c"=>"blue");
$a2=array("d"=>$obj1,"b"=>"black","e"=>"blue");
$result=UtilArray::arrayDiff($a1,$a2);
print_r($result);
print "hi";
*/