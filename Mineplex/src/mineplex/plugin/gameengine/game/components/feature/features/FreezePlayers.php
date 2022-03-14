<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/9/2015
 * Time: 10:07 PM
 */

namespace mineplex\plugin\gameengine\game\components\feature\features;


use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\game\components\feature\ListenerFeature;
use mineplex\plugin\gameengine\game\components\spectate\SpectateComponent;
use pocketmine\entity\Effect;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Player;

class FreezePlayers extends ListenerFeature {

    private $spectateComponent;

    function __construct(Arena $arena, SpectateComponent $spectateComponent = null)
    {
        parent::__construct($arena);
        $this->spectateComponent = $spectateComponent;
    }

    function onMove(PlayerMoveEvent $event)
    {
        if (!$this->hasPlayer($event->getPlayer()))
            return;

        if ($event->getFrom()->getFloorX() != $event->getTo()->getFloorX() || $event->getFrom()->getFloorZ() != $event->getTo()->getFloorZ())
            $event->setCancelled();
    }

    public function enable()
    {
        parent::enable();

        $effect = new Effect(Effect::SLOWNESS, "Waiting till game start...", 0, 0, 0, true);
        //TODO fix the 429496729
        $effect->setVisible(false)->setAmplifier(9)->setDuration(429496729);

        print "Player Count: " . count($this->getPlayers()) . "\n";

        foreach ($this->getPlayers() as $player)
        {
            print "Adding effect!\n";
            $player->addEffect($effect);
        }

    }

    public function disable()
    {
        parent::disable();
        print "Player Count: " . count($this->getPlayers()) . "\n";
        foreach ($this->getPlayers() as $player)
        {
            print "Remove effect! \n";
            $player->removeEffect(Effect::SLOWNESS);
        }
    }


    /**
     * @return Player[]
     */
    private function getPlayers()
    {
        if ($this->spectateComponent !== null)
            return $this->spectateComponent->getNonSpectators();
        else
            return $this->getArena()->getPlayers();
    }

    /**
     * @param Player $player
     * @return bool
     */
    private function hasPlayer(Player $player)
    {
        if ($this->spectateComponent !== null)
            return $this->spectateComponent->isNotSpectating($player);
        else
            return $this->getArena()->hasPlayer($player);
    }
}