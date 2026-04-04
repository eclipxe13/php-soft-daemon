<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\PcntlSignals;

use Eclipxe\SoftDaemon\PcntlSignals;

/**
 * Wrapper class to pcntl used by SoftDaemon
 * Do not put any logic on this class, it is only used to make system calls
 * @codeCoverageIgnore
 */
final class PhpPcntlSignals implements PcntlSignals
{
    /** @var list<int> */
    protected array $signals;

    public function __construct(int ...$signals)
    {
        $this->signals = array_values($signals);
    }

    /**
     * block signals using pcntl_sigprocmask
     * This is not covered on test because it creates a php system call
     */
    public function block(): bool
    {
        return pcntl_sigprocmask(SIG_BLOCK, $this->signals);
    }

    /**
     * unblock signals using pcntl_sigprocmask
     * This is not covered on test because it creates a php system call
     */
    public function unblock(): bool
    {
        return pcntl_sigprocmask(SIG_UNBLOCK, $this->signals);
    }

    /**
     * wait for blocked signals using pcntl_sigtimedwait
     * This is not covered on test because it creates a php system call
     *
     * @param int $seconds Numbers of seconds to wait
     */
    public function wait(int $seconds): int
    {
        $siginfo = [];
        return pcntl_sigtimedwait($this->signals, $siginfo, $seconds) ?: 0;
    }
}
