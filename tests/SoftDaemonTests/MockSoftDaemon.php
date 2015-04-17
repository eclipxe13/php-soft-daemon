<?php

namespace SoftDaemonTests;

use SoftDaemon\SoftDaemon;
use SoftDaemon\Executable;
use SoftDaemon\Sequencer;

class MockSoftDaemon extends SoftDaemon
{

    public $messages = [];

    public function addMessage($message)
    {
        $this->messages[] = $message;
    }

    public function __construct(Executable $executable, Sequencer $sequencer = null, $maxwait = self::DEFAULT_MAXWAIT, $minwait = self::DEFAULT_MINWAIT)
    {
        parent::__construct($executable, $sequencer, $maxwait, $minwait);
        $this->pcntlsignals = new MockPcntlSignals($this->signals);
    }

    public function exposeWaitTime($seconds)
    {
        return $this->waitTime($seconds);
    }

    public function setErrorCounter($counter)
    {
        $this->errorcount = $counter;
    }

    public function exposeSignalHandler($signo)
    {
        $this->signalHandler($signo);
    }

    protected function signalHandler($signo)
    {
        $this->addMessage("Signal $signo received");
        parent::signalHandler($signo);
    }

    public function terminate() {
        $this->addMessage('Terminate called');
        parent::terminate();
    }

    /**
     * @return MockPcntlSignals
     */
    public function exposePcntlSignals()
    {
        return $this->pcntlsignals;
    }

}
