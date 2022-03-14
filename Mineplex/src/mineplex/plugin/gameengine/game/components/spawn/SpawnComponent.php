<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/6/2015
 * Time: 2:06 PM
 */

namespace mineplex\plugin\gameengine\game\components\spawn;

use pocketmine\level\Position;
use pocketmine\Player;

interface SpawnComponent {

    /**
     * @param Player $player
     * @return Position
     */
    function respawn(Player $player);

    function respawnAll();

}