<?php

namespace SoftDaemon\Sequencers;

use SoftDaemon\Sequencer;

/**
 * Exponential sequencer calculate = count ^ exp
 * @package SoftDaemon
 */
class Exponential implements Sequencer
{
    protected $base;

    public function __construct($base = 2)
    {
        $this->base = max(2, is_numeric($base) ? intval($base) : 2);
    }

    public function getBase()
    {
        return $this->base;
    }

    public function calculate($count)
    {
        return pow($this->base, $count) - 1;
    }
}
