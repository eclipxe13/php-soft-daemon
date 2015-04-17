<?php

namespace SoftDaemon\Sequencers;

use SoftDaemon\Sequencer;

/**
 * Fixed sequencer calculate = fixed value
 * @package SoftDaemon
 */
class Fixed implements Sequencer
{
    private $seconds;

    public function __construct($seconds = 1) {
        $this->seconds = max(0, is_numeric($seconds) ? intval($seconds) : 1);
    }

    public function getSeconds()
    {
        return $this->seconds;
    }

    public function calculate($count)
    {
        return $this->getSeconds();
    }

}
