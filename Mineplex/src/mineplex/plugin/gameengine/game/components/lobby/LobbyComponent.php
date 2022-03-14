<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/4/2015
 * Time: 11:10 PM
 */

namespace mineplex\plugin\gameengine\game\components\lobby;

use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\arenas\events\ArenaEndEvent;
use mineplex\plugin\gameengine\arenas\events\ArenaJoinEvent;
use mineplex\plugin\gameengine\game\components\feature\ListenerFeature;
use mineplex\plugin\gameengine\game\components\feature\UtilFeature;
use mineplex\plugin\gameengine\game\components\gamestate\events\GameStateChangeEvent;
use mineplex\plugin\gameengine\game\components\gamestate\GameState;
use mineplex\plugin\gameengine\time\BenchTaskData;
use mineplex\plugin\Main;
use mineplex\plugin\util\UtilTeleport;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\HandlerList;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;

class LobbyComponent implements Listener
{

    /** @var Arena */
    private $arena;

    /** @var String */
    public $world;

    private $duringLobbyGameState;

    private $spawn;
    /**
     * @param Arena $arena
     * @param Level $world
     */
    public function __construct(Arena $arena, $world = null)
    {
        if ($world == null)
            $world = Server::getInstance()->getDefaultLevel();

        $world->setTime(6000);
        $world->stopTime();

        $this->world = $world;

        $this->spawn = new Position(0, 103, 0, $world);

        $this->arena = $arena;
        $this->duringLobbyGameState = new DuringLobbyGameState($this);

        Server::getInstance()->getPluginManager()->registerEvents($this, $arena->getPlugin());
    }


    function sendToSpawn(Player $player)
    {
        $player->getInventory()->clearAll();
        $player->removeAllEffects();
        $player->setGamemode(Player::ADVENTURE);

        $player->setHealth($player->getMaxHealth());
        $player->resetFallDistance();

        //Main::sendLoc($player, $this->world->getSpawnLocation());

        $pos = $this->spawn->add(rand(-4, 4), 0, rand(-4, 4));

        UtilTeleport::teleport($player, new Position($pos->getX(), $pos->getY(), $pos->getZ(), $this->world));
        //Main::sendLoc($player);
    }


    public function onBlockBreak(BlockBreakEvent $event)
    {
        if ($event->getBlock()->getLevel() != $this->world)
            return;
        $event->setCancelled();
    }

    function onDamage(EntityDamageEvent $event)
    {
        if ($event->getEntity()->getLevel() !== $this->world)
            return;
        $event->setCancelled();
    }

    /**
     * @priority LOW
     * @param GameStateChangeEvent $event
     */
    public function gameStateChange(GameStateChangeEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;

        if ($event->getToGameState() == GameState::LOBBY)
        {
            if (!$this->duringLobbyGameState->isEnabled())
            {
                foreach ($this->getArena()->getPlayers() as $player)
                {
                    if ($player->getPosition()->getLevel() !== $this->world)
                        $this->sendToSpawn($player);
                }
                $this->duringLobbyGameState->enable();
            }
        }
        elseif ($event->getFromGameState() == GameState::LOBBY)
        {
            if ($this->duringLobbyGameState->isEnabled())
            {
                $this->duringLobbyGameState->disable();
            }
        }
    }

    /**
     * @priority HIGH
     * @param ArenaEndEvent $event
     */
    public function onGameEnd(ArenaEndEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;

        HandlerList::unregisterAll($this);

        if ($this->duringLobbyGameState->isEnabled())
            $this->duringLobbyGameState->disable();
    }
    function getArena()
    {
        return $this->arena;
    }
}

class DuringLobbyGameState extends ListenerFeature
{
    private $master;

    public function __construct(LobbyComponent $master)
    {
        parent::__construct($master->getArena());
        $this->master = $master;
    }

    /**
     * @ignoreCancelled true
     * @param EntityDamageEvent $event
     */
    function onDamage(EntityDamageEvent $event)
    {
        if (!$this->getArena()->hasPlayer($event->getEntity()))
            return;
        $event->setCancelled();
    }

    /**
     * @ignoreCancelled true
     * @param PlayerDropItemEvent $event
     */
    function onDrop(PlayerDropItemEvent $event)
    {
        if (!$this->getArena()->hasPlayer($event->getPlayer()))
            return;
        $event->setCancelled();
    }

    /**
     * @ignoreCancelled true
     * @param InventoryPickupItemEvent $event
     */
    function onPickUp(InventoryPickupItemEvent $event)
    {
        if (!$this->getArena()->hasPlayer($event->getInventory()->getHolder()))
            return;
        $event->setCancelled();
    }

    /**
     * @ignoreCancelled true
     * @param EntityShootBowEvent $event
     */
    function onBowShoot(EntityShootBowEvent $event)
    {
        if (!$this->getArena()->hasPlayer($event->getEntity()))
            return;

        $event->setCancelled();
    }

    /**
     * @ignoreCancelled true
     * @param ArenaJoinEvent $event
     */
    function onJoin(ArenaJoinEvent $event)
    {
        if ($this->getArena() !== $event->getArena())
            return;

        $this->master->sendToSpawn($event->getPlayer());
    }
}