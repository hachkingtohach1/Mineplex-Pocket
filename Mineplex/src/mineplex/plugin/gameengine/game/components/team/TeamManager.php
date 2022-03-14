<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/13/2015
 * Time: 4:41 AM
 */

namespace mineplex\plugin\gameengine\game\components\team;


use pocketmine\Player;

interface TeamManager {

    /**
     * @param String $name
     * @return Team|null
     */
    function getTeam($name);

    /**
     * @return Team[]
     */
    function getTeams();

    /**
     * @param Player $player
     * @return Team|null
     */
    function getPlayersTeam(Player $player);

    function setPlayersTeam(Player $player, Team $team);

}