<?php
/**
 * Created by PhpStorm.
 * User: TheMineBench
 * Date: 7/2/2015
 * Time: 2:01 PM
 */

namespace mineplex\plugin\gameengine\time;

use SplObjectStorage;

interface BenchTask {

    public function run(BenchTaskData $data);

}