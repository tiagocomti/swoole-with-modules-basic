<?php

namespace app\modules\api\interfaces;

use Prophecy\Doubler\Generator\Node\ClassNode;

interface iConditions
{
    /**
     * Checks if my conf are ok
     *
     * @return bool
     */
    public function checkConfig() :bool;
}