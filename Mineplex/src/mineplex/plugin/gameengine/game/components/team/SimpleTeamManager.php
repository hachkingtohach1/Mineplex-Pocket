<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/13/2015
 * Time: 5:04 AM
 */

namespace mineplex\plugin\gameengine\game\components\team;


use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\util\UtilArray;
use pocketmine\item\LeatherTunic;
use pocketmine\Player;
use InvalidArgumentException;

class SimpleTeamManager implements TeamManager {

    private $arena;

    /** @var  Team[] */
    private $teams = [];

    /**
     * @param Arena $arena
     * @param Team[] $teams
     */
    function __construct(Arena $arena, array $teams = [])
    {
        $this->arena = $arena;
        foreach ($teams as $team)
        {
            $this->addTeam($team);
        }
    }

    function addTeam(Team $team)
    {
        print "adding team: " . $team->getDisplayName() ."\n";
        $this->teams[$team->getName()] = $team;
    }

    /**
     * @param String $name
     * @return Team|null
     */
    function getTeam($name)
    {
        if (UtilArray::hasKey($name, $this->teams))
        {
            return $this->teams[$name];
        }
        return null;
    }

    /**
     * @return Team[]
     */
    function getTeams()
    {
        return $this->teams;
    }

    /**
     * @param Player $player
     * @return Team|null
     */
    function getPlayersTeam(Player $player)
    {
        foreach ($this->getTeams() as $team)
        {
            if ($team->hasPlayer($player))
                return $team;
        }
        return null;
    }

    function setPlayersTeam(Player $player, Team $team)
    {
        $oldTeam = $this->getPlayersTeam($player);
        if ($oldTeam != null)
        {
            $oldTeam->removePlayer_forUseByManagerOnly($player);
        }

        //Meh, why not?
        if (!in_array($team, $this->teams))
        {
            throw new InvalidArgumentException('The team giving is not known by this manager!');
        }

        $team->addPlayer_forUseByManagerOnly($player);

    }
}