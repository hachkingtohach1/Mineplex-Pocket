<?php
/**
 * Created by PhpStorm.
 * User: TheMineBench
 * Date: 6/30/2015
 * Time: 9:04 PM
 */
namespace mineplex\plugin\gameengine\game;

use mineplex\plugin\gameengine\arenas\Arena;

interface Game {
    public function start(Arena $arena);
}