<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon;

interface Sequencer
{
    public function calculate(int $count): int;
}
