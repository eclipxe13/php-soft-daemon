<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Tests\Unit;

use Eclipxe\SoftDaemon\Internal\PcntlSignals;

class MockPcntlSignals extends PcntlSignals
{
    /** @var string[] */
    public $messages = [];

    /** @var int[] */
    public $returnSignals = [0, 0, 0, SIGHUP, 0, SIGUSR1, SIGUSR2, SIGTERM];

    /** @var int */
    public $waitIterator = 0;

    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }

    public function signalsToString(): string
    {
        return '[' . implode(', ', $this->signals) . ']';
    }

    public function block(): bool
    {
        $this->addMessage('block ' . $this->signalsToString());
        return true;
    }

    public function unblock(): bool
    {
        $this->addMessage('unblock ' . $this->signalsToString());
        return true;
    }

    public function wait(int $seconds): int
    {
        $signal = $this->returnSignals[$this->waitIterator];
        $this->waitIterator = $this->waitIterator + 1;
        $this->addMessage("Wait for $seconds seconds, return $signal");
        return $signal;
    }
}
