<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/6/2015
 * Time: 1:03 PM
 */

namespace mineplex\plugin\gameengine\game\components\feature;

use mineplex\plugin\gameengine\arenas\Arena;
use pocketmine\event\HandlerList;
use pocketmine\event\Listener;
use pocketmine\Server;

class ListenerFeature implements Feature, Listener {

    /** @var bool */
    private $enabled = false;
    private $arena;

    function __construct(Arena $arena)
    {
        $this->arena = $arena;
    }

    public function enable()
    {
        $this->enabled = true;
        Server::getInstance()->getPluginManager()->registerEvents($this, $this->getArena()->getPlugin());
    }

    public function disable()
    {
        $this->enabled = false;
        HandlerList::unregisterAll($this);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return Arena
     */
    public function getArena()
    {
        return $this->arena;
    }

}