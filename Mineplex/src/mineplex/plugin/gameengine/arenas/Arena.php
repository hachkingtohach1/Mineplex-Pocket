<?php
/**
 * Created by PhpStorm.
 * User: Bench
 * Date: 6/30/2015
 * Time: 1:35 PM
 */
namespace mineplex\plugin\gameengine\arenas;

use pocketmine\plugin\Plugin;
use pocketmine\Player;

interface Arena
{
    /**
     * @param Player $player
     * @return bool
     */
    public function canJoin(Player $player);
    public function addPlayer(Player $player);
    public function removePlayer(Player $player);

    /**
     * @return Player[]
     */
    public function getPlayers();

    /**
     * @param mixed $player
     * @return bool
     */
    public function hasPlayer($player);

    /**
     * @param string $message
     * @return void
     */
    public function broadcast($message);

    /**
     * @return Plugin
     */
    public function getPlugin();

    public function  endGame();

}