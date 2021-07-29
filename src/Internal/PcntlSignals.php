<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Internal;

/**
 * Wrapper class to pcntl used by SoftDaemon
 * Do not put any logic on this class, it is only osed to make system calls
 * @access private
 * @package SoftDaemon
 */
class PcntlSignals
{
    /** @var int[] */
    protected $signals;

    /**
     * PcntlSignals constructor.
     *
     * @param int[] $signals
     */
    public function __construct(array $signals)
    {
        $this->signals = $signals;
    }

    /**
     * block signals using pcntl_sigprocmask
     * This is not covered on test because it create a php system call
     *
     * @return bool
     */
    public function block(): bool
    {
        return pcntl_sigprocmask(SIG_BLOCK, $this->signals); // @codeCoverageIgnore
    }

    /**
     * unblock signals using pcntl_sigprocmask
     * This is not covered on test because it create a php system call
     *
     * @return bool
     */
    public function unblock(): bool
    {
        return pcntl_sigprocmask(SIG_UNBLOCK, $this->signals); // @codeCoverageIgnore
    }

    /**
     * wait for blocked signals using pcntl_sigtimedwait
     * This is not covered on test because it create a php system call
     *
     * @param int $seconds Numbers of seconds to wait
     * @return int
     */
    public function wait(int $seconds): int
    {
        $siginfo = [];
        return pcntl_sigtimedwait($this->signals, $siginfo, $seconds) ?: 0; // @codeCoverageIgnore
    }
}
