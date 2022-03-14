<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/6/2015
 * Time: 12:55 PM
 */

namespace mineplex\plugin\gameengine\game\components\feature;


class UtilFeature {
    public static function enable(Feature $feature)
    {
        if (!$feature->isEnabled())
            $feature->enable();
    }

    public static function disable(Feature $feature)
    {
        if ($feature->isEnabled())
            $feature->disable();
    }
}