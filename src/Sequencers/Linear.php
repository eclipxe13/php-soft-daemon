<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Sequencers;

use Eclipxe\SoftDaemon\Sequencer;

/**
 * Fixed sequencer calculate = count
 * @package SoftDaemon
 */
class Linear implements Sequencer
{
    public function calculate(int $count): int
    {
        return $count;
    }
}
