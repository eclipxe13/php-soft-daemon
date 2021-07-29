<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Sequencers;

use Eclipxe\SoftDaemon\Sequencer;

/**
 * Fixed sequencer calculate = fixed value
 * @package SoftDaemon
 */
class Fixed implements Sequencer
{
    /** @var int */
    private $seconds;

    public function __construct(int $seconds = 1)
    {
        $this->seconds = max(0, $seconds);
    }

    public function getSeconds(): int
    {
        return $this->seconds;
    }

    public function calculate(int $count): int
    {
        return $this->getSeconds();
    }
}
