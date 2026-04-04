<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Examples;

use Eclipxe\SoftDaemon\Executable;

class ExampleExecutable implements Executable
{
    protected int $counter = 0;

    /** @param array<int, bool> $returns */
    public function __construct(protected array $returns)
    {
    }

    public function signalHandler(int $signo): void
    {
        echo 'ExampleExecutable process ', $signo, "\n";
        if (SIGHUP === $signo) {
            $this->counter = 0;
        }
    }

    public function runOnce(): bool
    {
        $return = ($this->returns[$this->counter] ?? false);
        echo "Try to run $this->counter time: ", ($return) ? 'TRUE' : 'FALSE', "\n";
        $this->counter = $this->counter + 1;
        return $return;
    }
}
