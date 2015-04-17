<?php

namespace SoftDaemonTests;

use SoftDaemon\Internal\PcntlSignals;

class MockPcntlSignals extends PcntlSignals
{

    public $messages = [];

    public $returnSignals = [0, 0, 0, SIGHUP, 0, SIGUSR1, SIGUSR2, SIGTERM];
    public $waitIterator = 0;

    public function addMessage($message)
    {
        $this->messages[] = $message;
    }

    public function signalsToString()
    {
        return "[".implode(', ', $this->signals)."]";
    }

    public function block() {
        $this->addMessage("block " . $this->signalsToString());
    }

    public function unblock()
    {
        $this->addMessage("unblock " . $this->signalsToString());
    }

    public function wait($seconds)
    {
        $signal = $this->returnSignals[$this->waitIterator];
        $this->waitIterator = $this->waitIterator + 1;
        $this->addMessage("Wait for $seconds seconds, return $signal");
        return $signal;
    }

}
