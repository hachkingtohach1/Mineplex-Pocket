<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/6/2015
 * Time: 10:32 PM
 */

namespace mineplex\plugin\gameengine\game\components\spawn;


use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\arenas\events\ArenaEndEvent;
use mineplex\plugin\gameengine\game\components\spectate\SpectateComponent;
use mineplex\plugin\gameengine\game\components\world\event\WorldLoadSuccessEvent;
use mineplex\plugin\gameengine\game\components\world\WorldComponent;
use mineplex\plugin\Main;
use mineplex\plugin\util\UtilArray;
use mineplex\plugin\util\UtilTeleport;
use pocketmine\event\HandlerList;
use pocketmine\event\Listener;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;

class SimpleSpawnComponent implements SpawnComponent, Listener {

    private $arena;
    private $worldComponent;
    private $gameMode;
    private $spectateComponent;
    /** @var  Position[] */
    private $spawns = [];

    function __construct(Arena $arena, WorldComponent $worldComponent, SpectateComponent $spectateComponent = null, $gameMode = Player::SURVIVAL)
    {

        $this->arena = $arena;
        $this->worldComponent = $worldComponent;
        $this->gameMode = $gameMode;
        $this->spectateComponent = $spectateComponent;

        Server::getInstance()->getPluginManager()->registerEvents($this, $arena->getPlugin());
    }

    public function onWorld(WorldLoadSuccessEvent $event)
    {
        print "WorldLoadSuccessEvent!";
        if ($event->getArena() !== $this->arena)
            return;

        $this->spawns = $this->worldComponent->getTeams()['Green'];

//        $this->spawns = UtilArray::getValuesRecursively($this->worldComponent->getTeams());
//
//        foreach ($this->spawns as $key => $value)
//        {
//            if (!($value instanceof Position))
//                unset($this->spawns[$key]);
//        }
//
//        print (count($this->spawns) . " spawns Loaded\n");

    }

    /**
     * @param Player $player
     * @return Position
     */
    function respawn(Player $player)
    {

        /*
        $player->getInventory()->clearAll();
        $player->removeAllEffects();
        $player->resetFallDistance();
        $player->setGamemode($this->gameMode);
        $player->setHealth($player->getMaxHealth());
        */

        $pos = $this->getSpawn();
        UtilTeleport::teleport($player, $pos);

        return $pos;
    }

    /**
     * @return null|Position
     */
    function getSpawn()
    {
        if (count($this->spawns) < 1)
        {
            print "no spawns!";
            return null;
        }
        /** @var Position $best */
        $best = null;
        $bestDist = 0;

        foreach ($this->spawns as $spawn)
        {

            $closestPlayer = -1;

            foreach ($this->getPlayers() as $player)
            {
                if ($player->getPosition()->getLevel() != $spawn->getLevel())
                    continue;
                //distanceSquared is a cheaper method, and as long as its consistent it will be fine
                $playerDist = $spawn->distanceSquared($player->getPosition());
                if ($playerDist < $closestPlayer || $closestPlayer < 0)
                {
                    $closestPlayer = $playerDist;
                }
            }
            if ($best == null || $closestPlayer > $bestDist)
            {
                $best = $spawn;
                $bestDist = $closestPlayer;
            }

        }
        return $best;
    }

    function respawnAll() {
        foreach ($this->arena->getPlayers() as $player)
        {
            $this->respawn($player);
        }
    }

    function onEnd(ArenaEndEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;
        HandlerList::unregisterAll($this);
    }

    function getPlayers()
    {
        if ($this->spectateComponent != null)
            return $this->spectateComponent->getNonSpectators();
        else
            return $this->arena->getPlayers();
    }

}