<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/3/2015
 * Time: 2:03 PM
 */

namespace mineplex\plugin\gameengine\time;



class BenchTaskData {

    /** @var BenchTask */
    private $task;
    /** @var Integer */
    private $period;
    /** @var Integer */
    private $nextRun;

    private $id;

    public function __construct(BenchTask $task, $period, $id)
    {
        $this->task = $task;
        $this->period = $period;
        $this->id = $id;
    }

    /**
     * @param int $period
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    }

    /**
     * @param int $nextRun
     */
    public function setNextRun($nextRun)
    {
        $this->nextRun = $nextRun;
    }

    /**
     * @return int
     */
    public function getNextRun()
    {
        return $this->nextRun;
    }

    /**
     * @return BenchTask
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @return int
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    public function end()
    {
        $this->period = null;
    }
}