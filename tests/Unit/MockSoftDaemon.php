<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Tests\Unit;

use Eclipxe\SoftDaemon\Executable;
use Eclipxe\SoftDaemon\Internal\PcntlSignals;
use Eclipxe\SoftDaemon\Sequencer;
use Eclipxe\SoftDaemon\SoftDaemon;

class MockSoftDaemon extends SoftDaemon
{
    /** @var string[] */
    public $messages = [];

    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }

    public function __construct(Executable $executable, Sequencer $sequencer = null, $maxwait = self::DEFAULT_MAXWAIT, $minwait = self::DEFAULT_MINWAIT)
    {
        parent::__construct($executable, $sequencer, $maxwait, $minwait);
        $this->pcntlsignals = new MockPcntlSignals($this->signals);
    }

    public function exposeWaitTime(int $seconds): int
    {
        return $this->waitTime($seconds);
    }

    public function setErrorCounter(int $counter): void
    {
        $this->errorcount = $counter;
    }

    public function exposeSignalHandler(int $signo): void
    {
        $this->signalHandler($signo);
    }

    protected function signalHandler(int $signo): void
    {
        $this->addMessage("Signal $signo received");
        parent::signalHandler($signo);
    }

    public function terminate(): void
    {
        $this->addMessage('Terminate called');
        parent::terminate();
    }

    public function exposePcntlSignals(): PcntlSignals
    {
        return $this->pcntlsignals;
    }
}
