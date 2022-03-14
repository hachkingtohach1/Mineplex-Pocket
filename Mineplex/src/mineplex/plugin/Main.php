<?php

namespace mineplex\plugin;

use mineplex\plugin\gameengine\arenas\MultiGameArena;
use mineplex\plugin\gameengine\game\components\world\WorldComponent;
use mineplex\plugin\gameengine\game\factory\TestGameFactory;
use mineplex\plugin\core\updater\Updater;
use mineplex\plugin\gameengine\time\BenchSchedule;
use mineplex\plugin\gameengine\time\BenchTask;
use mineplex\plugin\gameengine\time\BenchTaskData;
use mineplex\plugin\util\UtilFile;
use mineplex\plugin\util\UtilString;
use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use mineplex\plugin\gameengine\arenas\Arena;
use pocketmine\Server;

class Main extends PluginBase implements Listener, BenchTask
{
    /** @var Arena */
    private $arena;

    public function onEnable()
    {
        $this->arena = new MultiGameArena($this, new TestGameFactory());
        Server::getInstance()->getPluginManager()->registerEvents($this, $this);

        new Updater($this);

        $this->arena->getPlugin()->getServer()->getLevelByName("world")->setSpawnLocation(new Vector3(0, 200, 0));
    }


    public function onLogin(PlayerLoginEvent $event)
    {
        if ($this->arena->canJoin($event->getPlayer()))
            return;

        $event->setKickMessage("Unable to join game!");
        $event->setCancelled();
    }

    //This is a hack to allow people to teleport a player when the join... I know it's scurry D:
    public function onJoin(PlayerJoinEvent $event)
    {
        BenchSchedule::runTaskLaterWithId($this, 0, $event->getPlayer());
    }

    function run(BenchTaskData $task)
    {
        if (!($task->getId() instanceof Player))
            return;

        /** @var Player $player */
        $player = $task->getId();

        if (!$player->isConnected())
            return;

        $this->arena->addPlayer($player);
    }


    public function command(PlayerCommandPreprocessEvent $event)
    {
        if (UtilString::startsWith($event->getMessage(), "/pos"))
            self::sendLoc($event->getPlayer());
    }

    public static function sendLoc(Player $player, Position $pos = null)
    {
        if ($pos == null)
            $pos = $player->getLocation();

        $player->sendMessage("X: " . $pos->getX());
        $player->sendMessage("Y: " . $pos->getY());
        $player->sendMessage("Z: " . $pos->getZ());
        $player->sendMessage("Level: " . $pos->getLevel()->getName());
    }

}