<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/4/2015
 * Time: 1:12 PM
 */

namespace mineplex\plugin\gameengine\game\factory;

use mineplex\plugin\gameengine\game\Game;

interface GameFactory {
    /**
     * @return Game
     */
    public function getGame();
}