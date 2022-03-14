<?php
/**
 * Created by PhpStorm.
 * User: TheMineBench
 * Date: 6/30/2015
 * Time: 9:18 PM
 */
namespace mineplex\plugin\gameengine\game\games\pvp;

use mineplex\plugin\gameengine\game\components\countdown\GameStateCountdown;
use mineplex\plugin\gameengine\game\components\feature\features\NoBlockBreak;
use mineplex\plugin\gameengine\game\components\feature\GameStateFeatureManager;
use mineplex\plugin\gameengine\game\components\gamestate\GameState;
use mineplex\plugin\gameengine\game\components\gamestate\GameStateComponent;
use pocketmine\event\Listener;
use mineplex\plugin\gameengine\game\Game;
use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\game\components\feature\Feature;
use pocketmine\item\Item;

class Pvp implements Game, Listener {

    public function start(Arena $arena)
    {
        $gameStateComponent = new GameStateComponent($arena);

        $noBlockBreak = new NoBlockBreak($arena, array(Item::GRASS));

        /** @var Feature[][] $features */
        $features = array(
            GameState::LOBBY => array($noBlockBreak),
            GameState::PRE_GAME => array($noBlockBreak),
            GameState::GAME => array($noBlockBreak),
            GameState::POST_GAME => array($noBlockBreak)
        );

        new GameStateFeatureManager($arena, $features);

        new GameStateCountdown($arena, $gameStateComponent, 20, GameState::LOBBY, GameState::PRE_GAME);

        new GameStateCountdown($arena, $gameStateComponent, 10, GameState::PRE_GAME, GameState::GAME);

        new GameStateCountdown($arena, $gameStateComponent, 30, GameState::GAME, GameState::POST_GAME);

        new GameStateCountdown($arena, $gameStateComponent, 10, GameState::POST_GAME, GameState::RESTARTING);
    }

}