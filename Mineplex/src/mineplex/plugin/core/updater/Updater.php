<?php
/**
 * Created by PhpStorm.
 * User: jwilliams
 * Date: 6/29/2015
 * Time: 6:45 PM
 */

namespace mineplex\plugin\core\updater;

use mineplex\plugin\core\updater\UpdateType;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;

class Updater extends PluginTask
{
    private $plugin;
    /** @var UpdateType[] */
    private $updateTypes;

    public function __construct(PluginBase $host)
    {
        parent::__construct($host);
        $this->plugin = $host;

        $this->plugin->getServer()->getScheduler()->scheduleRepeatingTask($this, 1);

        $this->updateTypes = array(
            new UpdateType(UpdateType::MS125)
            , new UpdateType(UpdateType::MS250)
            , new UpdateType(UpdateType::MS500)
            , new UpdateType(UpdateType::S1)
            , new UpdateType(UpdateType::S2)
            , new UpdateType(UpdateType::S4)
            , new UpdateType(UpdateType::S8)
            , new UpdateType(UpdateType::S16)
            , new UpdateType(UpdateType::S32)
            , new UpdateType(UpdateType::M1)
            , new UpdateType(UpdateType::M2)
            , new UpdateType(UpdateType::M4)
            , new UpdateType(UpdateType::M8)
            , new UpdateType(UpdateType::M16)
            , new UpdateType(UpdateType::M32));
    }

    //Fires off an event each tick, containing a list of all updateTypes
    public function onRun($currentTick)
    {
        $updateTypes = array();

        foreach ($this->updateTypes as &$updateType)
        {
            if ($updateType->canTrigger())
            {
                $updateTypes[$updateType->time] = 1;
            }
        }
        //Call Event
        $this->plugin->getServer()->getPluginManager()->callEvent(new UpdateEvent($currentTick, $updateTypes));
    }
}