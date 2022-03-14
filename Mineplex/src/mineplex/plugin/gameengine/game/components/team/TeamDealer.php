<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/13/2015
 * Time: 6:21 AM
 */

namespace mineplex\plugin\gameengine\game\components\team;


use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\arenas\events\ArenaEndEvent;
use mineplex\plugin\gameengine\game\components\gamestate\events\GameStateChangeEvent;
use mineplex\plugin\gameengine\game\components\gamestate\GameState;
use pocketmine\event\HandlerList;
use pocketmine\event\Listener;
use pocketmine\Server;

class TeamDealer implements Listener {

    private $arena;
    private $teamManager;


    function __construct(Arena $arena, TeamManager $teamManager)
    {
        $this->arena = $arena;
        $this->teamManager = $teamManager;
        Server::getInstance()->getPluginManager()->registerEvents($this, $arena->getPlugin());
    }

    public function gameState(GameStateChangeEvent $event)
    {
        if ($event->getArena() != $this->arena)
            return;

        if ($event->getToGameState() == GameState::PRE_GAME)
            $this->deal();

    }

    function deal()
    {
        $teams = array_values($this->teamManager->getTeams());

        $counter = 0;

        foreach ($this->arena->getPlayers() as $player)
        {

            $counter++;
            Server::getInstance()->broadcastMessage("counter: " . ($counter));
            Server::getInstance()->broadcastMessage("count: " . count($teams));
            Server::getInstance()->broadcastMessage("total: " . ($counter % count($teams)));
            $this->teamManager->setPlayersTeam($player, $teams[$counter % count($teams)]);

        }
    }

    function gameEnd(ArenaEndEvent $event)
    {
        if ($event->getArena() != $this->arena)
            return;
        HandlerList::unregisterAll($this);
    }
}