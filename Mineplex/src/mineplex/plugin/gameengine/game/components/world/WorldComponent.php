<?php
/**
 * Created by PhpStorm.
 * User: C
 * Date: 5/07/2015
 * Time: 12:27 PM
 */

namespace mineplex\plugin\gameengine\game\components\world;

use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\arenas\events\ArenaEndEvent;
use mineplex\plugin\gameengine\arenas\events\ArenaStartEvent;
use mineplex\plugin\gameengine\game\components\world\event\WorldLoadFailEvent;
use mineplex\plugin\gameengine\game\components\world\event\WorldLoadSuccessEvent;
use mineplex\plugin\gameengine\time\BenchSchedule;
use mineplex\plugin\gameengine\time\BenchTask;
use mineplex\plugin\gameengine\time\BenchTaskData;
use mineplex\plugin\util\UtilArray;
use mineplex\plugin\util\UtilString;
use mineplex\plugin\util\UtilFile;
use pocketmine\event\HandlerList;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;

use ZipArchive;
use Exception;

class WorldComponent implements Listener, BenchTask
{
    private $arena;

    private $gameId;
    private $worldNameFolder;

    /** @var \pocketmine\level\Level */
    private $world = null;

    private $mapName;
    private $mapAuthor;

    private $mapTeams = array();
    private $mapData = array();
    private $mapSettings = array();

    private $ready = false;

    public function __construct(Arena $arena)
    {
        $this->arena = $arena;

        $this->gameId = $this->getNewGameId();

        Server::getInstance()->getPluginManager()->registerEvents($this, $arena->getPlugin());
    }

    public function onStart(ArenaStartEvent $event)
    {
        if ($this->arena !== $event->getArena())
            return;
        $this->loadWorld("Survival Games");
    }

    private function unloadWorld()
    {
        if (is_null($this->world) || !$this->arena->getPlugin()->getServer()->isLevelLoaded($this->worldNameFolder))
            return;

        foreach ($this->world->getPlayers() as $player)
        {
            $player->kick("Dead World");
        }

        $this->arena->getPlugin()->getServer()->unloadLevel($this->world);

        UtilFile::deleteDir('worlds' . DIRECTORY_SEPARATOR . $this->worldNameFolder);

        print("Successfully Deleted: " . $this->worldNameFolder . "\n");
    }

    private function loadWorld($gameName)
    {
        $files = scandir('..' . DIRECTORY_SEPARATOR. '..' . DIRECTORY_SEPARATOR. 'update'. DIRECTORY_SEPARATOR .'maps' . DIRECTORY_SEPARATOR . $gameName . '/');

        $maps = array();

        foreach ($files as $file)
        {
            if (UtilString::endsWith($file, ".zip"))
            {
                array_push($maps, $file);
            }
        }

        $worldName = $maps[rand(0, count($maps) - 1)];

        //Trim .zip
        $worldName = substr($worldName, 0, strlen($worldName) - 4);

        print_r($worldName . "\n");

        $this->worldNameFolder = "Game" . $this->gameId . "_" . $gameName . "_" . $worldName;

        //Unzip World
        $zip = new ZipArchive;
        $res = $zip->open('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'update' . DIRECTORY_SEPARATOR. 'maps' . DIRECTORY_SEPARATOR . $gameName . DIRECTORY_SEPARATOR . $worldName . '.zip');
        if ($res === TRUE)
        {
            $zip->extractTo('worlds' . DIRECTORY_SEPARATOR . $this->worldNameFolder . '/');
            $zip->close();
            print("Successfully Extracted: " . $this->worldNameFolder . "\n");
        }
        else
        {
            print("Error Extracting: " . $this->worldNameFolder . "\n");

            Server::getInstance()->getPluginManager()->callEvent(new WorldLoadFailEvent($this->arena));

            return;
        }

        //Load World
        if ($this->arena->getPlugin()->getServer()->loadLevel($this->worldNameFolder))
        {
            $this->world = $this->arena->getPlugin()->getServer()->getLevelByName($this->worldNameFolder);

            $this->world->setSpawnLocation(new Vector3(0,200,0));
            $this->world->setTime(6000);
            $this->loadWorldData();
            
            $this->ready = true;

            print("Successfully Loaded World: " . $this->worldNameFolder . "\n");

            Server::getInstance()->getPluginManager()->callEvent(new WorldLoadSuccessEvent($this->arena, $this->world));
        }
        else
        {
            print("Error Loading World: " . $this->worldNameFolder . "\n");

            Server::getInstance()->getPluginManager()->callEvent(new WorldLoadFailEvent($this->arena));

            $this->arena->endGame();
        }
    }

    public function loadWorldData()
    {
        $handle = fopen('worlds' . DIRECTORY_SEPARATOR . $this->worldNameFolder . DIRECTORY_SEPARATOR.  'WorldConfig.dat', "r");
        if ($handle)
        {
            //These store the array that data should be inserted into

            $currentTeamName = null;
            $currentDataName = null;

            while (($line = fgets($handle)) !== false)
            {
                $trimmedLine = trim($line, "\n\r");

                $tokens = explode(":", $trimmedLine);

                if (count($tokens) < 2 || strlen($tokens[0]) == 0)
                {
                    continue;
                }

                //Name & Author
                if (strcmp($tokens[0], "MAP_NAME") === 0)
                {
                    $this->mapName = $tokens[1];
                }
                elseif (strcmp($tokens[0], "MAP_AUTHOR") === 0)
                {
                    $this->mapAuthor = $tokens[1];
                }

                //Map Boundaries
                elseif (strcmp($tokens[0], "MIN_X") === 0 ||
                    strcmp($tokens[0], "MAX_X") === 0 ||
                    strcmp($tokens[0], "MIN_Y") === 0 ||
                    strcmp($tokens[0], "MAX_Y") === 0 ||
                    strcmp($tokens[0], "MIN_Z") === 0 ||
                    strcmp($tokens[0], "MAX_Z") === 0)
                {
                    $this->mapSettings[$tokens[0]] = $tokens[1];
                }

                //Team Spawns
                elseif (strcmp($tokens[0], "TEAM_NAME") === 0)
                {
                    $currentTeamName = $tokens[1];
                }
                elseif (strcmp($tokens[0], "TEAM_SPAWNS") === 0)
                {
                    $positions = array();

                    for ($x=1 ; $x<count($tokens) ; $x++)
                    {
                        $position = $this->strToPos($tokens[$x]);

                        if (is_null($position))
                            continue;

                        $this->posTest = $position;

                        array_push($positions, $position);
                    }

                    $this->mapTeams[$currentTeamName] = $positions;
                }

                //Data
                elseif (strcmp($tokens[0], "DATA_NAME") === 0)
                {
                    $currentDataName = $tokens[1];
                }
                elseif (strcmp($tokens[0], "DATA_LOCS") === 0)
                {
                    $positions = array();

                    for ($x=1 ; $x<count($tokens) ; $x++)
                    {
                        $position = $this->strToPos($tokens[$x]);

                        if (is_null($position))
                            continue;

                        array_push($positions, $position);
                    }

                    $this->mapData[$currentDataName] = $positions;
                }
            }

            fclose($handle);
        }
        else
        {
            print("Error Opening File.");
        }
    }

    /**
     * @return Position[][]
     */
    public function getTeams()
    {
        return $this->mapTeams;
    }

    /**
     * @param $key
     * @return int
     */
    public function getSetting($key)
    {
        return $this->mapSettings[$key];
    }

    /**
     * @param $key
     * @return Position[]
     */
    public function getData($key)
    {
        if (UtilArray::hasKey($key, $this->mapData))
            return $this->mapData[$key];

        return [];
    }

    protected function strToPos($str)
    {
        if (strlen($str) < 5)
            return null;

        $tokens = explode(",", $str);

        try
        {
            return new Position($tokens[0], $tokens[1], $tokens[2], $this->world);
        }
        catch (Exception $e)
        {
            print("World Data Read Error: Invalid Position String [" . $str . "]\n");
        }

        return null;
    }

    //This will return a UID for the game
    public function getNewGameId()
    {
        return rand(0, 999999);                                             //Make this actually unique
    }

    public function isWorldReady()
    {
        return $this->ready;
    }

    public function onEnd(ArenaEndEvent $event)
    {
        if ($this->arena !== $event->getArena())
            return;
        HandlerList::unregisterAll($this);
        BenchSchedule::runTaskLater($this, (1000 * 10));
    }

    public function run(BenchTaskData $bench)
    {
        $this->unloadWorld();
    }

    public function getWorld()
    {
        return $this->world;
    }
}

