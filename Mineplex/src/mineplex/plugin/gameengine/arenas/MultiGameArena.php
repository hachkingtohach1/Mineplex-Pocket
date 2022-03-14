<?php
/**
 * Created by PhpStorm.
 * User: Bench
 * Date: 7/4/2015
 * Time: 12:14 PM
 */

namespace mineplex\plugin\gameengine\arenas;

use mineplex\plugin\gameengine\arenas\events\ArenaEndEvent;
use mineplex\plugin\gameengine\game\factory\GameFactory;
use mineplex\plugin\util\UtilArray;
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


class MultiGameArena implements Arena, Listener
{
    /** @var Player[] */
    private $players = array();

    private $plugin;

    private $gameFactory;
    private $game;


    public function __construct(Plugin $plugin, GameFactory $gameFactory)
    {
        Server::getInstance()->getPluginManager()->registerEvents($this, $plugin);
        $this->gameFactory = $gameFactory;
        $this->plugin = $plugin;
        $this->startGame();
    }

    public function canJoin(Player $player)
    {
        $event = new ArenaCanJoinEvent($this, $player);
        $this->plugin->getServer()->getPluginManager()->callEvent($event);
        return !$event->isCancelled();
    }

    public function addPlayer(Player $player)
    {
        if (!UtilArray::hasKey($player->getName(), $this->players))
        {
            $this->plugin->getServer()->getPluginManager()->callEvent(new ArenaJoinEvent($this, $player));
            $this->players[$player->getName()] = $player;
        }
    }

    public function removePlayer(Player $player)
    {
        if (UtilArray::hasKey($player->getName(), $this->players))
        {
            $this->plugin->getServer()->getPluginManager()->callEvent(new ArenaQuitEvent($this, $player));
            unset($this->players[$player->getName()]);
        }
    }

    public function onQuit(PlayerQuitEvent $event)
    {
        $this->removePlayer($event->getPlayer());
    }


    public function endGame()
    {
        Server::getInstance()->broadcastMessage("Game Over!");
        Server::getInstance()->getPluginManager()->callEvent(new ArenaEndEvent($this));
        $this->startGame();

    }

    private function startGame()
    {
        $this->game = $this->gameFactory->getGame();
        $this->game->start($this);
        Server::getInstance()->getPluginManager()->callEvent(new ArenaStartEvent($this));
    }

    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param mixed $player
     * @return bool
     */
    public function hasPlayer($player)
    {
        if (!($player instanceof Player))
            return false;
        return in_array($player, $this->getPlayers());
    }

    public function broadcast($message)
    {
        foreach ($this->getPlayers() as $player)
        {
            $player->sendMessage($message);
        }
    }

    public function getCurrentGame()
    {
        return $this->game;
    }

    public function getPlugin()
    {
        return $this->plugin;
    }
}