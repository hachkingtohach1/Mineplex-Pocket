<?php
/**
 * Created by PhpStorm.
 * User: TheMineBench
 * Date: 7/1/2015
 * Time: 10:18 PM
 */

namespace mineplex\plugin\gameengine\game\components\feature;


interface Feature {

    public function enable();

    public function disable();
    /**
     * @return boolean
     */
    public function isEnabled();

}