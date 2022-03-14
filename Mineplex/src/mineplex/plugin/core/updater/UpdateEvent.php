<?php
/**
 * Created by PhpStorm.
 * User: jwilliams
 * Date: 6/29/2015
 * Time: 7:12 PM
 */

namespace mineplex\plugin\core\updater;

use pocketmine\event\Event;

class UpdateEvent extends Event
{
    public static $handlerList = null;

    public $tick;
    public $updateTypes;

    public function __construct($tick, $updateTypes)
    {
        $this->tick = $tick;
        $this->updateTypes = $updateTypes;
    }

    public function getTick()
    {
        return $this->tick;
    }

    public function isTiming($type)
    {
        return (isset($this->updateTypes[$type]) || array_key_exists($type, $this->updateTypes));
    }
}