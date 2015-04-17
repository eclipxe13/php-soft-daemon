<?php

namespace SoftDaemon\Internal;

/**
 * Wrapper class to pcntl used by SoftDaemon
 * Do not put any logic on this class, it is only osed to make system calls
 * @access private
 * @package SoftDaemon
 */
class PcntlSignals
{
    protected $signals;

    public function __construct(array $signals)
    {
        $this->signals = $signals;
    }

    /**
     * block signals using pcntl_sigprocmask
     * This is not covered on test because it create a php system call
     * @codeCoverageIgnore
     * @return bool
     */
    public function block()
    {
        return \pcntl_sigprocmask(SIG_BLOCK, $this->signals);
    }

    /**
     * unblock signals using pcntl_sigprocmask
     * This is not covered on test because it create a php system call
     * @codeCoverageIgnore
     * @return bool
     */
    public function unblock()
    {
        return \pcntl_sigprocmask(SIG_UNBLOCK, $this->signals);
    }

    /**
     * wait for blocked signals using pcntl_sigtimedwait
     * This is not covered on test because it create a php system call
     * @codeCoverageIgnore
     * @param int $seconds Numbers of seconds to wait
     * @return bool
     */
    public function wait($seconds)
    {
        $siginfo = [];
        return \pcntl_sigtimedwait($this->signals, $siginfo, $seconds);
    }

}
