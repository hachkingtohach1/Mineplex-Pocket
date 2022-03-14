<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/13/2015
 * Time: 3:56 AM
 */

namespace mineplex\plugin\gameengine\game\components\team;

use pocketmine\Player;

interface Team {

    /**
     * @return string
     */
    function getName();

    /**
     * @return string
     */
    function getDisplayName();


    function getColor();

}
