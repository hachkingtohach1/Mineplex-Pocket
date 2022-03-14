<?php
/**
 * Created by PhpStorm.
 * User: jwilliams
 * Date: 6/29/2015
 * Time: 6:52 PM
 */

namespace mineplex\plugin\core\updater;


class UpdateType
{
    const M64 = 3840000;
    const M32 = 1920000;
    const M16 = 960000;
    const M8 = 480000;
    const M4 = 240000;
    const M2 = 120000;
    const M1 = 60000;
    const S32 = 32000;
    const S16 = 16000;
    const S8 = 8000;
    const S4 = 4000;
    const S2 = 2000;
    const S1 = 1000;
    const MS500 = 500;
    const MS250 = 250;
    const MS125 = 125;

    public $time;

    private $lastTrigger = 0;

    public function __construct($time=0)
    {
        $this->time = $time;
    }

    public function canTrigger()
    {
        if (round(microtime(true) * 1000) - $this->lastTrigger > $this->time)
        {
            $this->lastTrigger = round(microtime(true) * 1000);
            return true;
        }

        return false;
    }

    public function isTiming($timing)
    {
        return $this->time === $timing;
    }

}