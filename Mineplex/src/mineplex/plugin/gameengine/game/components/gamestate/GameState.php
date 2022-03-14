<?php
/**
 * Created by PhpStorm.
 * User: TheMineBench
 * Date: 7/1/2015
 * Time: 10:33 AM
 */

namespace mineplex\plugin\gameengine\game\components\gamestate;


//Yes yes I know this is a horrible way of doing things, I'm just trying to get done fast.
class GameState {

    const LOBBY = 0;
    const PRE_GAME = 1;
    const GAME = 2;
    const POST_GAME = 3;
    const RESTARTING = 4;

}