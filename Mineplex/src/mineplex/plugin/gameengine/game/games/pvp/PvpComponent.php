<?php
/**
 * Created by PhpStorm.
 * User: TheMineBench
 * Date: 6/30/2015
 * Time: 10:15 PM
 */

namespace mineplex\plugin\gameengine\game\games\pvp;

use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\event\HandlerList;
use pocketmine\event\player\PlayerDeathEvent;
use mineplex\plugin\gameengine\arenas\events\ArenaQuitEvent;
use mineplex\plugin\gameengine\arenas\events\ArenaJoinEvent;
use mineplex\plugin\gameengine\arenas\events\ArenaEndEvent;
use mineplex\plugin\gameengine\arenas\Arena;



class PvpComponent implements Listener {

    private $arena;

    public function __construct(Arena $arena)
    {
        Server::getInstance()->getPluginManager()->registerEvents($this, $arena->getPlugin());
        $this->arena = $arena;
    }

    public function onJoin(ArenaJoinEvent $event)
    {

        if ($event->getArena() !== $this->arena)
            return;
        $event->getPlayer()->sendMessage("Welcome to the arena");


        $players = $this->arena->getPlayers();

        array_push($players, $event->getPlayer());
        Server::getInstance()->broadcastMessage("JoinCount: ".(string)count($players));
        if (count($this->arena->getPlayers()) >= 2)
        {
            foreach ($this->arena->getPlayers() as $player)
            {
                $player->sendPopup("Game started!  Try to kill everyone else.");
            }
        }
    }

    public function onDeath(PlayerDeathEvent $event)
    {
        if (in_array($event->getEntity(), $this->arena->getPlayers()))
            $event->getEntity()->kick('You done got yourself keeled!');

    }

    public function onQuit(ArenaQuitEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;

        Server::getInstance()->broadcastMessage(" has quit.");
        $this->checkEnd();
    }

    public function onEnd(ArenaEndEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;
        Server::getInstance()->broadcastMessage("Unregistered!");
        HandlerList::unregisterAll($this);
    }

    public function checkEnd()
    {
        Server::getInstance()->broadcastMessage("Count: ".(string)(count($this->arena->getPlayers()) <= 1) ? 'true' : 'false');

        if (count($this->arena->getPlayers()) <= 1)
        {
            Server::getInstance()->broadcastMessage('game ending...');
            $this->arena->endGame();
        }
    }


}