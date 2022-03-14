<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/13/2015
 * Time: 5:55 AM
 */

namespace mineplex\plugin\gameengine\game\components\team;


use mineplex\plugin\util\UtilArray;
use pocketmine\Player;

class SimpleTeam implements Team {

    /** @var Player[] */
    private $players = [];

    private $name;
    private $color;

    /**
     * @param string $name
     * @param string $color
     */
    function __construct($name, $color)
    {
        $this->name = $name;
        $this->color = $color;
    }

    function addPlayer_forUseByManagerOnly(Player $player)
    {
        $this->players[$player->getName()] = $player;
        $player->sendMessage("$this->color You are now on the team $this->name!");
    }

    function removePlayer_forUseByManagerOnly(Player $player)
    {
        if ($this->hasPlayer($player))
            unset ($this->players[$player->getName()]);
    }

    /**
     * @return Player[]
     */
    function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param Player $player
     * @return bool
     */
    function hasPlayer(Player $player)
    {
        return UtilArray::hasKey($player->getName(), $this->players);
    }

    /**
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    function getDisplayName()
    {
        return $this->getColor() . $this->getName();
    }

    /**
     * @return string
     */
    function getColor()
    {
        return $this->color;
    }

}

