<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/5/2015
 * Time: 11:52 PM
 */

namespace mineplex\plugin\gameengine\game\components\spectate;


use pocketmine\Player;

interface SpectateComponent {
    /**
     * @param Player $player
     * @return bool
     */
    public function enableSpectate(Player $player);

    /**
     * @param Player $player
     * @return bool
     */
    public function disableSpectate(Player $player);

    /**
     * @param Player $player
     * @return bool
     */
    public function isSpectating(Player $player);

    /**
     * @param Player $player
     * @return bool
     */
    public function isNotSpectating(Player $player);

    /**
     * @return Player[]
     */
    public function getSpectators();

    /**
     * @return Player[]
     */
    public function getNonSpectators();

}
