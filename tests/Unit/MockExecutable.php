<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Tests\Unit;

use Eclipxe\SoftDaemon\Executable;

class MockExecutable implements Executable
{
    /** @var list<string> */
    public array $messages = [];

    public int $time = 0;

    /** @var list<bool> */
    public array $runValues = [];

    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }

    public function runOnce(): bool
    {
        $this->time = $this->time + 1;
        $return = $this->runValues[$this->time - 1] ?? false;
        $this->addMessage(sprintf('Run %d will return %s', $this->time, $return ? 'true' : 'false'));
        return $return;
    }

    public function signalHandler(int $signo): void
    {
        $this->addMessage("Executable signal $signo on $this->time");
    }
}
