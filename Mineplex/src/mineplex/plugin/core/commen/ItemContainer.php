<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/6/2015
 * Time: 6:12 PM
 */

namespace mineplex\plugin\core\commen;

use mineplex\plugin\util\UtilArray;

class ItemContainer {

    /** @var int[] */
    private $ids = [];

    /** @var bool */
    private $black;

    /**
     * @param int[] $ids
     * @param bool $black
     */
    public function __construct(array $ids = null, $black = false)
    {
        if ($ids != null)
        {
            $this->ids = array_flip($ids);
        }
        $this->black = $black;
    }

    public function hasItem($id)
    {
        return ($this->black == UtilArray::hasKey($id, $this->ids));
    }
}