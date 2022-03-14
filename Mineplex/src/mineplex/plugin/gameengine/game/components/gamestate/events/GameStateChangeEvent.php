<?php
/**
 * Created by PhpStorm.
 * User: TheMineBench
 * Date: 7/1/2015
 * Time: 10:49 AM
 */

namespace mineplex\plugin\gameengine\game\components\gamestate\events;

use mineplex\plugin\gameengine\arenas\ArenaEvent;
use mineplex\plugin\gameengine\arenas\Arena;

class GameStateChangeEvent extends ArenaEvent {

    public static $handlerList = null;

    private $fromGameState;
    private $toGameState;

    public function __construct(Arena $arena, $fromGameState, $toGameState)
    {
        parent::__construct($arena);
        $this->fromGameState = $fromGameState;
        $this->toGameState = $toGameState;
    }

    public function getFromGameState()
    {
        return $this->fromGameState;
    }

    public function getToGameState()
    {
        return $this->toGameState;
    }

}