<?php
/**
 * Created by PhpStorm.
 * User: TheMineBench
 * Date: 7/2/2015
 * Time: 1:53 PM
 */

namespace mineplex\plugin\gameengine\time;

use pocketmine\scheduler\Task;
use pocketmine\Server;

class BenchSchedule extends Task
{
    /** @var BenchSchedule  */
    private static $instance = null;

    /**
     * @return BenchSchedule
     */
    private static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new BenchSchedule();
        }
        return self::$instance;
    }

    /** @var  ActualBenchTask[] */
    private $tasks = [];

    private function __construct()
    {
        Server::getInstance()->getScheduler()->scheduleRepeatingTask($this, 1);
    }

    public function onRun($currentTick)
    {
        $currentTime = round(microtime(true) * 1000);

        foreach ($this->tasks as $key => $task) {

            if (!($currentTime >= $task->getNextRun()))
                continue;

            $task->getTaskData()->getTask()->run($task->getTaskData());

            if ($task->getTaskData()->getNextRun() !== null)
            {
                $task->setNextRun($task->getTaskData()->getNextRun());
                $task->getTaskData()->setNextRun(null);
            } elseif ($task->getTaskData()->getPeriod() == null or $task->getTaskData()->getPeriod() < 0) {
                unset($this->tasks[$key]);
            } else {
                $task->setNextRun($currentTime + $task->getTaskData()->getPeriod());
            }
        }
    }


    /**
     * @param BenchTask $taskToCancel
     * @return bool
     */
    public static function cancelTask(BenchTask $taskToCancel)
    {
        $deleted = false;
        foreach (self::getInstance()->tasks as $key => $task)
        {
            if ($task->getTaskData()->getTask() === $taskToCancel)
            {
                unset (self::getInstance()->tasks[$key]);
                $deleted = true;
            }
        }
        return $deleted;
    }

    /**
     * @param BenchTask $taskToCancel
     * @param $id
     * @return bool
     */
    public static function cancelTaskWithId(BenchTask $taskToCancel, $id)
    {
        $deleted = false;
        foreach (self::getInstance()->tasks as $key => $task)
        {
            if ($task->getTaskData()->getTask() === $taskToCancel && $task->getTaskData()->getId() === $id)
            {
                unset (self::getInstance()->tasks[$key]);
                $deleted = true;
            }
        }
        return $deleted;
    }

    /**
     * @param BenchTask $taskToCancel
     * @return bool
     */
    public static function isRunning(BenchTask $taskToCancel)
    {
        foreach (self::getInstance()->tasks as $key => $task)
        {
            if ($task->getTaskData()->getTask() === $taskToCancel)
            {
                return true;
            }
        }
        return false;
    }

    /**
     * @param BenchTask $taskToCancel
     * @param $id
     * @return bool
     */
    public static function isRunningWithId(BenchTask $taskToCancel, $id)
    {
        foreach (self::getInstance()->tasks as $key => $task)
        {
            if ($task->getTaskData()->getTask() === $taskToCancel && $task->getTaskData()->getId() === $id)
            {
                return true;
            }
        }
        return false;
    }

    /**
     * @param BenchTask $task
     * @param int $wait
     * @param int $period
     * @param $id
     */
    public static function runTaskTimerWithId(BenchTask $task, $wait, $period, $id)
    {
        $taskData = new BenchTaskData($task, $period, $id);
        $actualTask = new ActualBenchTask($taskData, round(microtime(true) * 1000) + $wait);
        array_push(self::getInstance()->tasks, $actualTask);
    }

    /**
     * @param BenchTask $task
     * @param int $wait
     * @param int $period
     */
    public static function runTaskTimer(BenchTask $task, $wait, $period)
    {
        self::runTaskTimerWithId($task, $wait, $period, null);
    }

    /**
     * @param BenchTask $task
     * @param int $wait
     * @param $id
     */
    public static function runTaskLaterWithId(BenchTask $task, $wait, $id)
    {
        self::runTaskTimerWithId($task, $wait, null, $id);
    }

    /**
     * @param BenchTask $task
     * @param int $wait
     */
    public static function runTaskLater(BenchTask $task, $wait)
    {
        self::runTaskTimerWithId($task, $wait, null, null);
    }

}

class ActualBenchTask
{
    /** @var int */
    private $nextRun;

    /** @var BenchTaskData */
    private $benchTaskData;

    public function __construct(BenchTaskData $benchTaskData, $nextRun)
    {
        $this->benchTaskData = $benchTaskData;
        $this->runNext = $nextRun;
    }

    /**
     * @return int
     */
    function getNextRun()
    {
        return $this->nextRun;
    }

    /**
     * @param $nextRun
     */
    function setNextRun($nextRun)
    {
        $this->nextRun = $nextRun;
    }

    /**
     * @return BenchTaskData
     */
    function getTaskData()
    {
        return $this->benchTaskData;
    }

}
