<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/5/2015
 * Time: 7:41 PM
 */

namespace mineplex\plugin\gameengine\game\components\countdown;

use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\arenas\events\ArenaEndEvent;
use mineplex\plugin\gameengine\arenas\events\ArenaJoinEvent;
use mineplex\plugin\gameengine\arenas\events\ArenaQuitEvent;
use mineplex\plugin\gameengine\game\components\gamestate\events\GameStateChangeEvent;
use mineplex\plugin\gameengine\game\components\gamestate\GameStateComponent;
use mineplex\plugin\gameengine\game\components\world\event\WorldLoadSuccessEvent;
use mineplex\plugin\gameengine\game\components\world\WorldComponent;
use mineplex\plugin\gameengine\time\BenchSchedule;
use mineplex\plugin\gameengine\time\BenchTask;
use mineplex\plugin\gameengine\time\BenchTaskData;
use pocketmine\event\HandlerList;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;

class LobbyCountdown implements Listener, BenchTask {

    private $startCount;
    private $count;
    private $gameStateComponent;
    /** @var WorldComponent */
    private $worldComponent;
    private $arena;
    private $startGameState;
    private $setGameState;
    private $minPlayers;

    private $message;

    const POPUP_ID = "popup";
    const COUNTDOWN_ID = "count";


    public function __construct(Arena $arena, GameStateComponent $gameStateComponent, $worldComponent = null, $startGameState, $setGameState, $count, $minPlayers = 2)
    {
        $this->arena = $arena;
        $this->gameStateComponent = $gameStateComponent;
        $this->worldComponent = $worldComponent;
        $this->minPlayers = $minPlayers;

        $this->startCount = $count;
        $this->count =  $count;

        $this->startGameState = $startGameState;
        $this->setGameState = $setGameState;

        Server::getInstance()->getPluginManager()->registerEvents($this, $arena->getPlugin());

        $this->checkCountdown();
    }


    /**
     * @param int $offset
     */
    function checkCountdown($offset = 0)
    {
        $playerCount = (count($this->arena->getPlayers()) + $offset);

        if ($this->gameStateComponent->getGameState() == $this->startGameState && ($this->worldComponent == null || $this->worldComponent->isWorldReady()) && $playerCount >= $this->minPlayers)
        {
            if (!BenchSchedule::isRunningWithId($this, self::COUNTDOWN_ID))
            {
                $this->count = $this->startCount;
                $this->setCount();
                BenchSchedule::runTaskTimerWithId($this, 1000, 1000, self::COUNTDOWN_ID);
            }
        }
        else
        {
            $this->setWaiting($playerCount);
            BenchSchedule::cancelTaskWithId($this, self::COUNTDOWN_ID);
        }
    }

    public function onJoin(ArenaJoinEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;
        $this->checkCountdown(1);
        $this->popup($event->getPlayer());
    }

    public function onQuit(ArenaQuitEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;
        if ($this->gameStateComponent->getGameState() == $this->startGameState)
            $this->checkCountdown(-1);
    }



    public function onWorldCreation(WorldLoadSuccessEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;
        $this->checkCountdown();
    }

    public function onGameStateChange(GameStateChangeEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;

        if ($event->getToGameState() == $this->startGameState)
        {
            $this->checkCountdown();
            BenchSchedule::runTaskTimerWithId($this, 500, 500, self::POPUP_ID);

        }
        elseif ($event->getFromGameState() == $this->startGameState)
        {
            //Cancels both tasks
            BenchSchedule::cancelTask($this);

            $this->count = $this->startCount;

        }

    }


    public function onEnd(ArenaEndEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;
        HandlerList::unregisterAll($this);
    }

    public function run(BenchTaskData $data)
    {
        if ($data->getId() == self::POPUP_ID)
        {
            $this->popupAll();
        }
        else //if ($data->getId() == self::COUNTDOWN_ID)
        {
            if ($this->count <= 0)
            {
                $this->gameStateComponent->setGameState($this->setGameState);
                $data->end();
                return;
            }

            $this->message = "§9Game starting in:§c $this->count";
            $this->count--;
        }
    }

    private function setWaiting($playerCount = null)
    {
        if ($playerCount == null)
            $playerCount = count($this->arena->getPlayers());
        $this->message = "§2Waiting for players! §a($playerCount/$this->minPlayers)";
        $this->popupAll();
    }

    private function setCount($count = null)
    {
        if ($count == null)
            $count = $this->count;
        $this->message = "§9Game starting in:§c $count";
        $this->popupAll();
    }

    private function popupAll()
    {
        foreach ($this->arena->getPlayers() as $player)
        {
            $this->popup($player);
        }
    }

    private function popup(Player $player)
    {
        $player->sendTip($this->message);
    }
}