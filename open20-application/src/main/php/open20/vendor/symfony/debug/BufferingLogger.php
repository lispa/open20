<?php

/*
 *
 * (l) Fabien Potencier <fabien@symfony.com>
 *
 */

namespace Symfony\Component\Debug;

use Psr\Log\AbstractLogger;

/**
 * A buffering logger that stacks logs for later.
 *
 */
class BufferingLogger extends AbstractLogger
{
    private $logs = array();

    public function log($level, $message, array $context = array())
    {
        $this->logs[] = array($level, $message, $context);
    }

    public function cleanLogs()
    {
        $logs = $this->logs;
        $this->logs = array();

        return $logs;
    }
}
