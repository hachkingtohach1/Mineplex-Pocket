<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/4/2015
 * Time: 3:09 PM
 */

namespace mineplex\plugin\gameengine\game\games\sg;

use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\game\components\countdown\GameStateCountdown;
use mineplex\plugin\gameengine\game\components\countdown\LobbyCountdown;
use mineplex\plugin\gameengine\game\components\feature\features\DeathSpectate;
use mineplex\plugin\gameengine\game\components\feature\features\FreezePlayers;
use mineplex\plugin\gameengine\game\components\feature\features\JoinSpectate;
use mineplex\plugin\gameengine\game\components\feature\features\NoBlockBreak;
use mineplex\plugin\gameengine\game\components\feature\features\NoBlockPlace;
use mineplex\plugin\gameengine\game\components\feature\features\NoDamage;
use mineplex\plugin\gameengine\game\components\feature\features\NoDropItem;
use mineplex\plugin\gameengine\game\components\feature\features\NoPickUpItem;
use mineplex\plugin\gameengine\game\components\feature\features\SpectateInGameWorld;
use mineplex\plugin\gameengine\game\components\feature\managers\GameStateFeatureManager;
use mineplex\plugin\gameengine\game\components\gamestate\GameState;
use mineplex\plugin\gameengine\game\components\gamestate\GameStateComponent;
use mineplex\plugin\gameengine\game\components\lobby\LobbyComponent;
use mineplex\plugin\gameengine\game\components\spawn\SimpleSpawnComponent;
use mineplex\plugin\gameengine\game\components\spawn\SpawnAt;
use mineplex\plugin\gameengine\game\components\spectate\GameModeSpectateComponent;
use mineplex\plugin\gameengine\game\components\team\SimpleTeam;
use mineplex\plugin\gameengine\game\components\team\SimpleTeamManager;
use mineplex\plugin\gameengine\game\components\team\TeamDealer;
use mineplex\plugin\gameengine\game\components\victorytype\LMSVictoryType;
use mineplex\plugin\gameengine\game\components\world\TimeComponent;
use mineplex\plugin\gameengine\game\components\world\WorldComponent;
use mineplex\plugin\gameengine\game\Game;

class SurvivalGames implements Game {

    function start(Arena $arena)
    {
        $gameStateComponent = new GameStateComponent($arena);

        $spectateComponent = new GameModeSpectateComponent($arena);

        $worldComponent = new WorldComponent($arena);



        $teamManager = new SimpleTeamManager($arena, [new SimpleTeam('Bench', '§6'), new SimpleTeam('Not Bench', '§b')]);
        new TeamDealer($arena, $teamManager);

        new ChestComponent($arena, $worldComponent);

        new LobbyComponent($arena);

        new TimeComponent($arena, $worldComponent);

        //Features start----
        $noDamage = new NoDamage($arena);

        $joinSpectate = [new JoinSpectate($arena, $spectateComponent), new SpectateInGameWorld($arena, $worldComponent, $spectateComponent)];

        $stopEveryThing = array(new NoBlockBreak($arena), new NoBlockPlace($arena),new NoDropItem($arena), new NoPickUpItem($arena));


        $features = array(

            GameState::PRE_GAME => array( $stopEveryThing, $noDamage, $joinSpectate, new FreezePlayers($arena, $spectateComponent)),

            GameState::GAME => array(     $stopEveryThing,            $joinSpectate , new DeathSpectate($arena, $spectateComponent)),

            GameState::POST_GAME => array($stopEveryThing, $noDamage, $joinSpectate),

        );

        new GameStateFeatureManager($arena, $features);
        //Features end---



        new SpawnAt($arena, new SimpleSpawnComponent($arena, $worldComponent), [GameState::PRE_GAME]);

        new LobbyCountdown(    $arena, $gameStateComponent, $worldComponent, GameState::LOBBY, GameState::PRE_GAME, 10, 2);



        new GameStateCountdown($arena, $gameStateComponent, 20, GameState::PRE_GAME, GameState::GAME);


        new LMSVictoryType(    $arena, $spectateComponent, $gameStateComponent);

        new GameStateCountdown($arena, $gameStateComponent, 5, GameState::POST_GAME, GameState::RESTARTING);

    }
}