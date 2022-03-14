<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/7/2015
 * Time: 2:31 PM
 */

namespace mineplex\plugin\util;


use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\MovePlayerPacket;
use pocketmine\network\protocol\RespawnPacket;
use pocketmine\network\protocol\SetSpawnPositionPacket;
use pocketmine\network\protocol\StartGamePacket;
use pocketmine\Player;
use pocketmine\network\protocol\UpdateBlockPacket;

class UtilTeleport {

    /**
     * @param Player $player
     * @param Vector3|Position $pos
     */
    public static function teleport(Player $player, Vector3 $pos)
    {
        if ($pos instanceof Position)
        {
            $current = $player->getPosition();
            //
            // This CRAZY HACK is to remove Tile entities that seem to linger
            // whenever you teleport!
            //

            if ($current->getLevel() != $pos->getLevel()) {
                $player->noDamageTicks = 20;
                foreach ($current->getLevel()->getTiles() as $tile) {
                    $pk = new UpdateBlockPacket();
                    $pk->x = $tile->x;
                    $pk->y = $tile->y;
                    $pk->z = $tile->z;
                    $pk->block = 0;
                    $pk->meta = 0;
                    $player->dataPacket($pk);
                }
            }
        }

        $player->teleport($pos);
    }
}