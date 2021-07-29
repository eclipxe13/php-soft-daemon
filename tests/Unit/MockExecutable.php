<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Tests\Unit;

use Eclipxe\SoftDaemon\Executable;

class MockExecutable implements Executable
{
    /** @var string[] */
    public $messages = [];

    /** @var int */
    public $time = 0;

    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }

    public function runOnce(): bool
    {
        $this->time = $this->time + 1;
        $this->addMessage("Run $this->time");
        return false;
    }

    public function signalHandler(int $signo): void
    {
        $this->addMessage("Executable signal $signo on $this->time");
    }
}
