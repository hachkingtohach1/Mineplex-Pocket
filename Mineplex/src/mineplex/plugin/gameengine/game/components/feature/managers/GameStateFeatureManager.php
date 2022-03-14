<?php
/**
 * Created by PhpStorm.
 * User: TheMineBench
 * Date: 7/2/2015
 * Time: 12:44 AM
 */

namespace mineplex\plugin\gameengine\game\components\feature\managers;


use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\arenas\events\ArenaEndEvent;
use mineplex\plugin\gameengine\game\components\gamestate\events\GameStateChangeEvent;
use mineplex\plugin\gameengine\game\components\gamestate\GameState;
use mineplex\plugin\util\UtilArray;
use pocketmine\event\HandlerList;
use pocketmine\event\Listener;
use pocketmine\Server;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use mineplex\plugin\gameengine\game\components\feature\Feature;

class GameStateFeatureManager implements Listener {

    private $arena;

    /** @var Feature[][] */
    private $features = [];

    /**
     * @param Arena $arena
     * @param Feature[] $features
     */
    public function __construct(Arena $arena, array $features)
    {
        $this->arena = $arena;

        unset ($features[GameState::RESTARTING]);

        foreach ($features as $key => $value)
        {

            $this->features[$key] = UtilArray::getValuesRecursively($value);
            print "$key: " . count($this->features[$key]) . "\n";
        }

        Server::getInstance()->getPluginManager()->registerEvents($this, $arena->getPlugin());
    }

    public function onGameStateChange(GameStateChangeEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;

        /** @var Feature[] $lastFeatures */

        if (isset($this->features[$event->getFromGameState()]) || array_key_exists($event->getFromGameState(), $this->features))
            $lastFeatures = $this->features[$event->getFromGameState()];
        else
            $lastFeatures = [];


        /** @var Feature[] $theseFeatures */

        if (isset($this->features[$event->getToGameState()]) || array_key_exists($event->getToGameState(), $this->features))
            $theseFeatures = $this->features[$event->getToGameState()];
        else
            $theseFeatures = [];

        print "\nLast Count: ". count($lastFeatures);
        print "\nThese Count: ". count($theseFeatures);


        /** @var Feature[] $toEnable */
        $toEnable = UtilArray::arrayDiff($theseFeatures, $lastFeatures);

        print "\nEnable Count: ". count($toEnable);

        /** @var Feature[] $toDisable */
        $toDisable = UtilArray::arrayDiff($lastFeatures, $theseFeatures);

        print "\nDisable Count: ". count($toDisable);

        print "\n";

        foreach ($toDisable as $feature)
        {
            if (in_array($feature, $theseFeatures))
                print "An error has happened!!\n";
            else
                print "All good ^_^\n";
        }

        foreach ($toDisable as $feature) {
            if ($feature->isEnabled())
            {
                print "Disable: " . get_class($feature) . spl_object_hash($feature) . "\n";
                $feature->disable();
            }
            else
            {
                print get_class($feature) . "\n" . "Is already disabled!" . "\n";
            }
        }

        foreach ($toEnable as $feature) {
            if (!$feature->isEnabled())
            {
                print "Enable: " . get_class($feature) . spl_object_hash($feature) . "\n";
                $feature->enable();
            }
            else
            {
                print get_class($feature) . "\n" . "Is already enabled!" . "\n";
            }

        }

    }

    public function onEnd(ArenaEndEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;

        $iterator = UtilArray::getValuesRecursively($this->features);

        foreach ($iterator as $feature) {
            if ($feature->isEnabled())
                $feature->disable();
        }

        HandlerList::unregisterAll($this);
    }

}


