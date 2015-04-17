<?php

class ExampleExecutable implements SoftDaemon\Executable
{
    protected $counter = 0;
    protected $returns;

    public function __construct(array $returns)
    {
        $this->returns = $returns;
    }

    public function signalHandler($signo)
    {
        echo 'ExampleExecutable process ', $signo, "\n";
        if ($signo === SIGHUP) {
            $this->counter = 0;
        }
    }

    public function runOnce()
    {
        $return = array_key_exists($this->counter, $this->returns) ? $this->returns[$this->counter] : false;
        echo "Try to run {$this->counter} time: ", ($return) ? 'TRUE' : 'FALSE', "\n";
        $this->counter = $this->counter + 1;
        return $return;
    }
}