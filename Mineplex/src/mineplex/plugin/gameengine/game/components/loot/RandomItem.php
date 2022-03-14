<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 14/07/2015
 * Time: 10:01
 */

namespace mineplex\plugin\gameengine\game\loot;

use mineplex\plugin\util\UtilMath;
use pocketmine\item\Item;

class RandomItem {

    private $chance;
    private $item;
    private $min;
    private $max;

    function __construct(Item $item, $chance, $min, $max)
    {
        $this->chance = $chance;
        $this->item = $item;
        $this->min = $min;
        $this->max = $max;
    }

    function getChance()
    {
        return $this->chance;
    }

    function getItem()
    {
        return new Item($this->item->getId(), 0, UtilMath::randBetween($this->min, $this->max), $this->item->getName());
    }

}