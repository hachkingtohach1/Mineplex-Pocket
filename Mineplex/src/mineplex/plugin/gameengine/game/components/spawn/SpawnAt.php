<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/6/2015
 * Time: 10:47 PM
 */

namespace mineplex\plugin\gameengine\game\components\spawn;


use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\arenas\events\ArenaEndEvent;
use mineplex\plugin\gameengine\game\components\gamestate\events\GameStateChangeEvent;
use pocketmine\event\HandlerList;
use pocketmine\event\Listener;
use pocketmine\Server;

class SpawnAt implements Listener {

    private $spawnComponent;
    private $arena;

    private $gameStates;

    function __construct(Arena $arena, SpawnComponent $spawnComponent, array $gameStates)
    {
        $this->arena = $arena;
        $this->spawnComponent = $spawnComponent;
        $this->gameStates = $gameStates;
        Server::getInstance()->getPluginManager()->registerEvents($this, $arena->getPlugin());
    }

    public function onStateChange(GameStateChangeEvent $event)
    {
        if ($this->arena !== $event->getArena())
            return;
        if (in_array($event->getToGameState(), $this->gameStates)) {
            $this->spawnComponent->respawnAll();
            print "called! \n";
        }
    }

    public function onEnd(ArenaEndEvent $event)
    {
        if ($this->arena !== $event->getArena())
            return;
        HandlerList::unregisterAll($this);
    }
}