<?php

namespace SoftDaemon;

interface Executable
{
    public function signalHandler($signo);
    public function runOnce();
}
