<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Examples;

use Eclipxe\SoftDaemon\Executable;

class ExampleExecutable implements Executable
{
    protected $counter = 0;

    protected $returns;

    public function __construct(array $returns)
    {
        $this->returns = $returns;
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
        $return = array_key_exists($this->counter, $this->returns) ? $this->returns[$this->counter] : false;
        echo "Try to run $this->counter time: ", ($return) ? 'TRUE' : 'FALSE', "\n";
        $this->counter = $this->counter + 1;
        return $return;
    }
}
