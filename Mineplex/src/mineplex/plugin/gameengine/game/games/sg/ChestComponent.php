<?php
/**
 * Created by PhpStorm.
 * User: WilliamTiger
 * Date: 7/14/2015
 * Time: 3:26 AM
 */

namespace mineplex\plugin\gameengine\game\games\sg;


use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\arenas\events\ArenaEndEvent;
use mineplex\plugin\gameengine\arenas\events\ArenaStartEvent;
use mineplex\plugin\gameengine\game\components\gamestate\events\GameStateChangeEvent;
use mineplex\plugin\gameengine\game\components\gamestate\GameState;
use mineplex\plugin\gameengine\game\components\loot\ChestLoot;
use mineplex\plugin\gameengine\game\components\world\event\WorldLoadSuccessEvent;
use mineplex\plugin\gameengine\game\components\world\WorldComponent;
use mineplex\plugin\util\UtilMath;
use pocketmine\event\HandlerList;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\math\Math;
use pocketmine\Server;
use pocketmine\tile\Chest;

class ChestComponent implements Listener {

    private $arena;
    private $worldComponent;
    private $loot;

    //You can put what you want in the constructor, but for chests this is all you should need.
    /**
     * @param Arena $arena
     * @param WorldComponent $worldComponent
     */
    function __construct(Arena $arena, WorldComponent $worldComponent)
    {
        $this->arena = $arena;
        $this->worldComponent = $worldComponent;
        Server::getInstance()->getPluginManager()->registerEvents($this, $arena->getPlugin());

        $this->loot = new ChestLoot();

        //Food
        $this->loot->addItemFull(Item::BAKED_POTATO, 30, 1, 3, "Baked Potato");
        $this->loot->addItemFull(Item::COOKED_BEEF, 30, 1, 2, "Steak");
        $this->loot->addItemFull(Item::COOKED_CHICKEN, 30, 1, 2, "Cooked Chicken");
        $this->loot->addItemFull(Item::CARROT, 30, 1, 3, "Carrot");
        $this->loot->addItemFull(Item::MUSHROOM_STEW, 15, 1, 1, "Mushroom Stew");
        $this->loot->addItemFull(Item::WHEAT, 30, 1, 6, "Wheat");
        $this->loot->addItemFull(Item::APPLE, 30, 1, 4, "Apple");
        $this->loot->addItemFull(Item::RAW_PORKCHOP, 30, 1, 4, "Pork");

        //Weapons
        $this->loot->addItem(Item::WOODEN_AXE, 80, 1, "Wooden Axe");
        $this->loot->addItem(Item::WOODEN_SWORD, 70, 1, "Wooden Sword");
        $this->loot->addItem(Item::STONE_AXE, 60, 1, "Stone Axe");
        $this->loot->addItem(Item::STONE_SWORD, 30, 1, "Stone Sword");

        //Leather Armour
        $this->loot->addItem(Item::LEATHER_BOOTS, 30, 1, "Leather Boots");
        $this->loot->addItem(Item::LEATHER_CAP, 30, 1, "Leather Cap");
        $this->loot->addItem(Item::LEATHER_PANTS, 30, 1, "Leather Pants");
        $this->loot->addItem(Item::LEATHER_TUNIC, 30, 1, "Leather Boots");

        //Gold Armour
        $this->loot->addItem(Item::GOLD_CHESTPLATE, 25, 1, "");
        $this->loot->addItem(Item::GOLD_LEGGINGS, 25, 1, "");
        $this->loot->addItem(Item::GOLD_BOOTS, 25, 1, "");
        $this->loot->addItem(Item::GOLD_HELMET, 25, 1, "");

        //Chain Armour
        $this->loot->addItem(Item::CHAIN_BOOTS, 20, 1, "");
        $this->loot->addItem(Item::CHAIN_CHESTPLATE, 20, 1, "");
        $this->loot->addItem(Item::CHAIN_LEGGINGS, 20, 1, "");
        $this->loot->addItem(Item::CHAIN_HELMET, 20, 1, "");

        //Throwable
        $this->loot->addItem(Item::BOW, 20, 1, "");
        $this->loot->addItemFull(Item::ARROW, 20, 1, 3, "");
        $this->loot->addItemFull(Item::SNOWBALL, 30, 1, 2, "");
        $this->loot->addItemFull(Item::EGG, 30, 1, 2, "");

        //Misc
        $this->loot->addItem(Item::COMPASS, 20, 1, "");
        $this->loot->addItemFull(Item::STICK, 30, 1, 2, "");
        $this->loot->addItem(Item::FLINT, 30, 1, 2, "");
        $this->loot->addItem(Item::FEATHER, 30, 1, 2, "");
        $this->loot->addItem(Item::GOLD_INGOT, 20, 1, "");
    }

    function onStart(ArenaStartEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;
        //Do stuff on Game start if you want
    }

    function onLoad(WorldLoadSuccessEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;

        //Called after the world is loaded

        foreach ($this->worldComponent->getData("54") as $loc)
        {
            $block = $loc->getLevel()->getBlock($loc);
            if ($block instanceof Chest)
            {
                $inv = $block->getInventory();

                $items = 2;
                if (UtilMath::random(100) > 50)
                    ++$items;
                if (UtilMath::random(100) > 65)
                    ++$items;
                if (UtilMath::random(100) > 80)
                    ++$items;
                if (UtilMath::random(100) > 95)
                    ++$items;

                for ($i = 0; $i < $items; $i++)
                {
                    $chosenItem = $this->loot->getLoot();
                    $inv->setItem(UtilMath::random(27), $chosenItem);
                }
            }
        }
    }

    function onStateChange(GameStateChangeEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;
        //Change this to what ever state you want
        if ($event->getToGameState() == GameState::GAME)
        {
            //Do this when the state becomes GAME
        }
        elseif ($event->getFromGameState() == GameState::GAME)
        {
            //Do this when the state is no longer GAME
        }
    }


    function onEnd(ArenaEndEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;

        HandlerList::unregisterAll($this);

        //Do end of game stuff if you need to
    }
}