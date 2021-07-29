<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon;

interface Executable
{
    public function signalHandler(int $signo): void;

    public function runOnce(): bool;
}
