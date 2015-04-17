<?php

namespace SoftDaemon\Sequencers;

use SoftDaemon\Sequencer;

/**
 * Fixed sequencer calculate = count
 * @package SoftDaemon
 */
class Linear implements Sequencer
{
    public function calculate($count)
    {
        return $count;
    }

}
