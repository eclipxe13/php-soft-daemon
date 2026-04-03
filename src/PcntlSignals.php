<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon;

interface PcntlSignals
{
    /**
     * block signals using pcntl_sigprocmask
     * This is not covered on test because it creates a php system call
     */
    public function block(): bool;

    /**
     * unblock signals using pcntl_sigprocmask
     * This is not covered on test because it creates a php system call
     */
    public function unblock(): bool;

    /**
     * wait for blocked signals using pcntl_sigtimedwait
     * This is not covered on test because it creates a php system call
     *
     * @param int $seconds Numbers of seconds to wait
     */
    public function wait(int $seconds): int;
}
