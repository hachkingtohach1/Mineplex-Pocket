<?php
/**
 * Created by PhpStorm.
 * User: Bench
 * Date: 6/30/2015
 * Time: 1:37 PM
 */
namespace mineplex\plugin\gameengine\arenas;

use mineplex\plugin\gameengine\arenas\events\ArenaEndEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\Server;
use mineplex\plugin\gameengine\game\Game;
use mineplex\plugin\gameengine\arenas\events\ArenaCanJoinEvent;
use mineplex\plugin\gameengine\arenas\events\ArenaJoinEvent;
use mineplex\plugin\gameengine\arenas\events\ArenaQuitEvent;
use mineplex\plugin\gameengine\arenas\events\ArenaStartEvent;

class SingleGameArena implements Arena, Listener {
    //I really have no idea if there is a better way to store players... but this is what I'm using for now.

    /** @var Player[] */
    private $players = array();

    private $game;
    private $plugin;

    public function __construct(Plugin $plugin, Game $game)
    {
        Server::getInstance()->getPluginManager()->registerEvents($this, $plugin);
        $this->game = $game;
        $this->plugin = $plugin;
        $this->getCurrentGame()->start($this);
        Server::getInstance()->getPluginManager()->callEvent(new ArenaStartEvent($this));
    }

    public function canJoin(Player $player)
    {
        $event = new ArenaCanJoinEvent($this, $player);
        $this->plugin->getServer()->getPluginManager()->callEvent($event);
        return !$event->isCancelled();
    }

    public function addPlayer(Player $player)
    {
        $this->plugin->getServer()->getPluginManager()->callEvent(new ArenaJoinEvent($this, $player));
        array_push($this->players, $player);
    }

    public function removePlayer(Player $player)
    {
        if(($key = array_search($player, $this->players, true)) !== FALSE) {
            unset($this->players[$key]);
        }
        Server::getInstance()->broadcastMessage("Calling ArenaQuitEvent");
        $this->plugin->getServer()->getPluginManager()->callEvent(new ArenaQuitEvent($this, $player));
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        if ($this->canJoin($event->getPlayer()))
        {
            $this->addPlayer($event->getPlayer());
        }
    }

    public function onQuit(PlayerQuitEvent $event)
    {
        $this->removePlayer($event->getPlayer());
    }




    public function getPlayers()
    {
        return $this->players;
    }

    public function getCurrentGame()
    {
        return $this->game;
    }

    public function getPlugin()
    {
        return $this->plugin;
    }

    public function endGame()
    {
        Server::getInstance()->getPluginManager()->callEvent(new ArenaEndEvent($this));
        Server::getInstance()->broadcastMessage("MooCount: ".(string)count($this->players));

        foreach ($this->players as $player)
        {
            Server::getInstance()->broadcastMessage((string)$player->getName());
            $player->kick('Game Over...', false);
        }
    }

}