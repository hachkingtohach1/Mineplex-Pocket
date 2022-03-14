<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/4/2015
 * Time: 3:03 PM
 */

namespace mineplex\plugin\gameengine\game\factory;


use mineplex\plugin\gameengine\game\games\sg\SurvivalGames;

class TestGameFactory implements GameFactory {

    function getGame()
    {
        return new SurvivalGames();
    }
}