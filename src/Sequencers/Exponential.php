<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Sequencers;

use Eclipxe\SoftDaemon\Sequencer;

/**
 * Exponential sequencer calculate = count ^ exp
 */
class Exponential implements Sequencer
{
    public const MIN_BASE = 2;

    /** @var int */
    protected $base;

    public function __construct(int $base = self::MIN_BASE)
    {
        $this->base = max(self::MIN_BASE, $base);
    }

    public function getBase(): int
    {
        return $this->base;
    }

    public function calculate(int $count): int
    {
        return $this->base ** $count - 1;
    }
}
