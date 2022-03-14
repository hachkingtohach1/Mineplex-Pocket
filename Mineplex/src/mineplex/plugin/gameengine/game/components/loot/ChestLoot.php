<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 14/07/2015
 * Time: 09:57
 */

namespace mineplex\plugin\gameengine\game\components\loot;

use mineplex\plugin\gameengine\game\loot\RandomItem;
use mineplex\plugin\util\UtilMath;
use pocketmine\item\Item;

class ChestLoot {

    private $randomItems = array();
    private $totalLoot;

    function __construct()
    {
        $this->totalLoot = 0;
    }

    function addItem($id, $chance, $size, $name)
    {
        $this->addLoot(new RandomItem(new Item($id, 0, 1, $name), $chance, $size, $size));
    }

    function addItemFull($id, $chance, $min, $max, $name)
    {
        $this->addLoot(new RandomItem(new Item($id, 0, 1, $name), $chance, $min, $max));
    }

    function addLoot($ri)
    {
        array_push($this->randomItems, $ri);
        $this->totalLoot += $ri->getChance();
    }

    function getLoot()
    {
        $num = UtilMath::random($this->totalLoot);

        foreach ($this->randomItems as $ri)
        {
            if ($ri instanceof RandomItem)
            {
                $num -= $ri->getChance();

                if ($num < 0)
                {
                    return $ri->getItem();
                }
            }
        }

        return null;
    }

}