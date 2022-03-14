<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/6/2015
 * Time: 1:27 AM
 */

namespace mineplex\plugin\gameengine\game\components\spectate\events;

use mineplex\plugin\gameengine\arenas\ArenaEvent;
use mineplex\plugin\gameengine\arenas\Arena;
use pocketmine\event\Cancellable;
use pocketmine\Player;

class EnableSpectateEvent extends ArenaEvent implements Cancellable {

    public static $handlerList = null;

    private $player;

    public function __construct(Arena $arena, Player $player)
    {
        parent::__construct($arena);
        $this->player = $player;
    }

    public function getPlayer()
    {
        return $this->player;
    }
}