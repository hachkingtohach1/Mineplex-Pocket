<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/7/2015
 * Time: 9:19 PM
 */

namespace mineplex\plugin\gameengine\game\components\victorytype;

use pocketmine\Player;
use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\game\components\spectate\SpectateComponent;
use mineplex\plugin\gameengine\game\components\gamestate\GameStateComponent;
use pocketmine\event\Listener;
use mineplex\plugin\gameengine\arenas\events\ArenaQuitEvent;
use mineplex\plugin\gameengine\game\components\spectate\events\EnableSpectateEvent;
use mineplex\plugin\gameengine\game\components\gamestate\GameState;
use mineplex\plugin\gameengine\game\components\feature\ListenerFeature;
use mineplex\plugin\gameengine\time\BenchTask;
use mineplex\plugin\gameengine\time\BenchSchedule;
use mineplex\plugin\gameengine\time\BenchTaskData;
use mineplex\plugin\gameengine\game\components\gamestate\events\GameStateChangeEvent;
use pocketmine\Server;
use mineplex\plugin\gameengine\game\components\feature\UtilFeature;
use mineplex\plugin\gameengine\arenas\events\ArenaEndEvent;
use pocketmine\event\HandlerList;

class LMSVictoryType implements Listener{

    private $arena;

    private $duringGame;

    function __construct(Arena $arena, SpectateComponent $spectateComponent, GameStateComponent $gameStateComponent, $endPlayersAmount = 1)
    {
        $this->arena = $arena;
        $this->duringGame = new DuringGame($arena, $spectateComponent, $gameStateComponent, $endPlayersAmount);

        Server::getInstance()->getPluginManager()->registerEvents($this, $arena->getPlugin());
    }


    public function gameStateChange(GameStateChangeEvent $event)
    {
        if ($this->arena !== $event->getArena())
            return;

        if ($event->getToGameState() == GameState::GAME)
        {
            UtilFeature::enable($this->duringGame);
        }
        elseif ($event->getFromGameState() == GameState::GAME)
        {
            UtilFeature::disable($this->duringGame);
        }
    }

    public function onEnd(ArenaEndEvent $event)
    {
        if ($this->arena !== $event->getArena())
            return;
        UtilFeature::disable($this->duringGame);
        HandlerList::unregisterAll($this);
    }

}

class DuringGame extends ListenerFeature implements BenchTask {

    /** @var Player[] */
    private $rank = [];

    /** @var Arena */
    private $arena;

    /** @var  SpectateComponent */
    private $spectateComponent;

    /** @var  GameStateComponent */
    private $gameStateComponent;

    /** @var  int */
    private $endPlayersAmount;

    function __construct(Arena $arena, SpectateComponent $spectateComponent, GameStateComponent $gameStateComponent, $endPlayersAmount = 1)
    {
        parent::__construct($arena);
        $this->arena = $arena;
        $this->spectateComponent = $spectateComponent;
        $this->gameStateComponent = $gameStateComponent;
        $this->endPlayersAmount = $endPlayersAmount;
    }

    /**
     * @param ArenaQuitEvent $event
     */
    public function onLeave(ArenaQuitEvent $event)
    {
        if ($this->arena !== $event->getArena())
            return;

        if (!$this->spectateComponent->isSpectating($event->getPlayer()))
            array_push($this->rank, $event->getPlayer());

        BenchSchedule::runTaskLater($this, 0);
    }


    public function onSpectate(EnableSpectateEvent $event)
    {
        if ($this->arena !== $event->getArena())
            return;

        array_push($this->rank, $event->getPlayer());

        BenchSchedule::runTaskLater($this, 0);
    }

    public function checkEndGame($subtract = false)
    {
        if ($this->gameStateComponent->getGameState() != GameState::GAME)
        {
            if ($this->isEnabled())
                $this->disable();
            return;
        }

        $count = count($this->spectateComponent->getNonSpectators()) - $subtract;

        if ($count <= $this->endPlayersAmount)
        {
            $this->gameStateComponent->setGameState(GameState::POST_GAME);
        }
    }

    private function sendWinners()
    {
        foreach ($this->spectateComponent->getNonSpectators() as $player)
        {
            array_push($this->rank, $player);
        }
        /** @var Player[] $rank */
        $rank = array_reverse($this->rank);

        $counter = 0;
        $this->arena->broadcast("----------");
        $this->arena->broadcast("");

        foreach ($rank as $player)
        {
            $counter++;
            $this->arena->broadcast("$counter. §e" . $player->getName());
            if ($counter >= 3)
                break;
        }
        $this->arena->broadcast("");
        $this->arena->broadcast("----------");

    }

    public function run(BenchTaskData $task)
    {
        $this->checkEndGame();
    }

    public function enable()
    {
        BenchSchedule::runTaskLater($this, 0);
        parent::enable();
    }

    public function disable()
    {
        $this->sendWinners();
        BenchSchedule::cancelTask($this);
        parent::disable();
    }
}