<?php
/**
 * Created by PhpStorm.
 * User: TheMineBench
 * Date: 7/1/2015
 * Time: 5:52 PM
 */

namespace mineplex\plugin\gameengine\game\components\countdown;

use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\arenas\events\ArenaEndEvent;
use mineplex\plugin\gameengine\game\components\gamestate\GameStateComponent;
use mineplex\plugin\gameengine\game\components\gamestate\events\GameStateChangeEvent;
use mineplex\plugin\gameengine\time\BenchSchedule;
use mineplex\plugin\gameengine\time\BenchTask;
use mineplex\plugin\gameengine\time\BenchTaskData;

use pocketmine\event\HandlerList;
use pocketmine\event\Listener;
use pocketmine\Server;


class GameStateCountdown implements Listener, BenchTask {

    private $startCount;
    private $count;
    private $gameStateComponent;
    private $arena;
    private $startGameState;
    private $setGameState;

    public function __construct(Arena $arena, GameStateComponent $gameStateComponent, $count, $startGameState, $setGameState)
    {

        $this->arena = $arena;
        $this->gameStateComponent = $gameStateComponent;

        $this->startCount = $count;
        $this->count =  $count;

        $this->startGameState = $startGameState;
        $this->setGameState = $setGameState;

        Server::getInstance()->getPluginManager()->registerEvents($this, $arena->getPlugin());
    }

    public function onGameStateChange(GameStateChangeEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;

        if ($event->getToGameState() == $this->startGameState)
        {
            BenchSchedule::runTaskTimer($this, 1000, 1000);
        }
            else
        {
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
        //print "§$this->startGameState---"."\n";
        $this->popup();
        if ($this->count <= 0)
        {
            $this->gameStateComponent->setGameState($this->setGameState);
            $data->end();
        }
        else
        {
            $this->count--;
        }


    }

    public function popup()
    {
        foreach ($this->arena->getPlayers() as $player)
        {
            $player->sendTip("Countdown: $this->count");
        }
    }

}