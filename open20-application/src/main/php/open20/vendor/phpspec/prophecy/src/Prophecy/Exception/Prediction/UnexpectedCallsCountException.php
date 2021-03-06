<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Exception\Prediction;

use Prophecy\Prophecy\MethodProphecy;

class UnexpectedCallsCountException extends UnexpectedCallsException
{
    private $expectedCount;

    public function __construct($message, MethodProphecy $methodProphecy, $count, array $calls)
    {
        parent::__construct($message, $methodProphecy, $calls);

        $this->expectedCount = intval($count);
    }

    public function getExpectedCount()
    {
        return $this->expectedCount;
    }
}
