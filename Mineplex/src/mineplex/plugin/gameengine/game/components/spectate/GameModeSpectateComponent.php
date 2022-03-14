<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/6/2015
 * Time: 12:53 AM
 */

namespace mineplex\plugin\gameengine\game\components\spectate;

use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\arenas\events\ArenaQuitEvent;
use mineplex\plugin\gameengine\game\components\spectate\events\DisableSpectateEvent;
use mineplex\plugin\gameengine\game\components\spectate\events\EnableSpectateEvent;
use mineplex\plugin\util\UtilArray;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;

class GameModeSpectateComponent implements SpectateComponent, Listener {

    /** @var  Player[] */
    private $spectators = [];

    private $arena;

    public function __construct(Arena $arena)
    {
        $this->arena = $arena;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function enableSpectate(Player $player)
    {
        if ($this->isSpectating($player))
            return false;
        $event = new EnableSpectateEvent($this->arena, $player);

        Server::getInstance()->getPluginManager()->callEvent($event);

        if ($event->isCancelled())
            return false;

        $player->getInventory()->clearAll();
        $player->removeAllEffects();

        $player->setHealth($player->getMaxHealth());
        $player->resetFallDistance();

        $player->setGamemode(Player::SPECTATOR);
        array_push($this->spectators, $player);
        return true;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function disableSpectate(Player $player)
    {
        if (!$this->isSpectating($player))
            return false;

        $event = new DisableSpectateEvent($this->arena, $player);

        Server::getInstance()->getPluginManager()->callEvent($event);

        if ($event->isCancelled())
            return false;

        if (($key = array_search($player, $this->spectators, true)) !== FALSE) {
            unset($this->spectators[$key]);
        }

        return true;
    }

    //HIGH so you can do isSpectating on ArenaQuitEvent
    /**
     * @priority HIGH
     * @param ArenaQuitEvent $event
     */
    public function onQuit(ArenaQuitEvent $event)
    {
        if ($this->arena !== $event->getArena())
            return;
        if ($this->isSpectating($event->getPlayer()))
        {
            unset($this->spectators[$event->getPlayer()->getName()]);
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isSpectating(Player $player)
    {
        return UtilArray::hasKey($player->getName(), $this->spectators);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isNotSpectating(Player $player)
    {
        return $this->arena->hasPlayer($player) && !$this->isSpectating($player);
    }

    /**
     * @return Player[]
     */
    public function getSpectators()
    {
        return $this->spectators;
    }

    /**
     * @return Player[]
     */
    public function getNonSpectators()
    {
        return UtilArray::arrayDiff($this->arena->getPlayers(), $this->getSpectators());
    }
}