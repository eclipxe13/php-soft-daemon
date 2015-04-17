<?php

namespace SoftDaemonTests;

use SoftDaemon\Executable;

class MockExecutable implements Executable
{

    public $messages = [];
    public $time = 0;

    public function runOnce()
    {
        $this->time = $this->time + 1;
        $this->addMessage("Run {$this->time}");
        return false;
    }

    public function signalHandler($signo)
    {
        $this->addMessage("Executable signal $signo on {$this->time}");
    }

    public function addMessage($message)
    {
        $this->messages[] = $message;
    }

}
