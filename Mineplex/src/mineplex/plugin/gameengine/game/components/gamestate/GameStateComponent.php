<?php
/**
 * Created by PhpStorm.
 * User: TheMineBench
 * Date: 7/1/2015
 * Time: 10:26 AM
 */

namespace mineplex\plugin\gameengine\game\components\gamestate;

use mineplex\plugin\gameengine\arenas\events\ArenaEndEvent;
use mineplex\plugin\gameengine\arenas\events\ArenaStartEvent;
use mineplex\plugin\gameengine\game\components\gamestate\events\GameStateChangeEvent;
use mineplex\plugin\gameengine\arenas\Arena;
use pocketmine\event\HandlerList;
use pocketmine\event\Listener;
use pocketmine\Server;

//require_once __DIR__ . '\GameState.php';

class GameStateComponent implements Listener {

    private $gameState;
    private $arena;

    public function __construct(Arena $arena)
    {
        $this->arena = $arena;
        $this->gameState = GameState::RESTARTING;
        Server::getInstance()->getPluginManager()->registerEvents($this, $arena->getPlugin());
    }

    public function onStart(ArenaStartEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;
        $this->setGameState(GameState::LOBBY);
    }

    /**
     * @priority LOW
     * @param ArenaEndEvent $event
     */
    public function onEnd(ArenaEndEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;
        HandlerList::unregisterAll($this);
        $this->localSetState(GameState::RESTARTING);
    }


    public function setGameState($gameState)
    {

        if ($gameState < GameState::LOBBY)
            $gameState = GameState::LOBBY;

        if ($gameState > GameState::RESTARTING)
            $gameState = GameState::RESTARTING;

        if ($gameState == $this->gameState)
            return false;

        if ($gameState == GameState::RESTARTING)
        {
            $this->arena->endGame();
            return true;
        }

        $this->localSetState($gameState);

        return true;
    }

    private function localSetState($gameState)
    {
        $oldGameState = $this->getGameState();

        $this->gameState = $gameState;

        $event = new GameStateChangeEvent($this->arena, $oldGameState, $gameState);

        //Not sure if I should call the event before of after...
        Server::getInstance()->getPluginManager()->callEvent($event);
    }

    public function getGameState()
    {
        return $this->gameState;
    }

}

//$john = new GameStateComponent();
//echo $john->getGameState();
