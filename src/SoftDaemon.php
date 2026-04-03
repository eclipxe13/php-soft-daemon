<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon;

use Eclipxe\SoftDaemon\PcntlSignals\PhpPcntlSignals;
use Eclipxe\SoftDaemon\Sequencers\Fixed as FixedSequencer;

class SoftDaemon
{
    /** maximum wait in seconds (1 hour) */
    public const DEFAULT_MAXWAIT = 3600;

    /** minimum wait in seconds (no wait) */
    public const DEFAULT_MINWAIT = 0;

    /** @var int minimal wait */
    protected int $minWait;

    /** @var int maximum wait */
    protected int $maxWait;

    /** @var int count of consecutive times the executable return error */
    protected int $errorCount = 0;

    /** @var bool pause state of the object */
    protected bool $pause = false;

    /** @var bool flag to control main loop */
    protected bool $mainLoop = false;

    /** @var PcntlSignals Native php functions (isolated) */
    protected PcntlSignals $pcntlSignals;

    /** @var list<int> Set of signals to block and wait for */
    protected const SIGNALS = [SIGHUP, SIGTERM, SIGINT, SIGQUIT, SIGUSR1, SIGUSR2];

    /**
     * @param Executable $executable Executable object
     * @param Sequencer $sequencer Sequencer object If null then a FixedSequencer(1) will be used
     * @param int $maxWait Maximum seconds to wait before call again the executable object (min: 1)
     * @param int $minWait Minimum seconds to wait before call again the executable object (min: 0)
     */
    public function __construct(
        protected Executable $executable,
        protected Sequencer $sequencer = new FixedSequencer(1),
        int $maxWait = self::DEFAULT_MAXWAIT,
        int $minWait = self::DEFAULT_MINWAIT,
    ) {
        $this->setMaxWait($maxWait);
        $this->setMinWait($minWait);
        $this->pcntlSignals = new PhpPcntlSignals(...static::SIGNALS);
    }

    /**
     * Set the max wait seconds, the SoftDaemon will not wait more than this quantity of seconds
     * Any value lower than 1 is fixed to 1
     */
    public function setMaxWait(int $maxWait): void
    {
        $this->maxWait = max(1, $maxWait);
    }

    /**
     * Get the max wait seconds
     */
    public function getMaxWait(): int
    {
        return $this->maxWait;
    }

    /**
     * Set the min wait seconds, the SoftDaemon will not wait less than this quantity of seconds
     * Any value lower than 0 is fixed to 0
     */
    public function setMinWait(int $minWait): void
    {
        $this->minWait = max(0, $minWait);
    }

    /**
     * Get the min wait seconds
     */
    public function getMinWait(): int
    {
        return $this->minWait;
    }

    /**
     * Reset the error counter to zero
     */
    public function resetErrorCounter(): void
    {
        $this->errorCount = 0;
    }

    /**
     * Will exit the main loop on the next iteration
     */
    public function terminate(): void
    {
        $this->mainLoop = false;
    }

    /**
     * Internally check if it must continue on main loop
     */
    protected function continueOnMainLoop(): bool
    {
        return $this->mainLoop;
    }

    /**
     * Count of consecutive times the executable return error
     * This value can only be set to zero using resetErrorCounter
     */
    public function getErrorCounter(): int
    {
        return $this->errorCount;
    }

    /**
     * Set the pause status, if on pause then main loop will only wait 1 second until another signal is received.
     * The executor is not called when the SoftDaemon is on pause.
     * The time to wait on pause is 1 second, but this is fixed to min wait and max wait.
     */
    public function setPause(bool $pause): void
    {
        $this->pause = $pause;
    }

    /**
     * Get the pause status
     */
    public function getPause(): bool
    {
        return $this->pause;
    }

    /**
     * Fix the wait time to force min wait and max wait
     */
    protected function waitTime(int $seconds): int
    {
        return max($this->minWait, min($this->maxWait, $seconds));
    }

    /**
     * Run the executor expecting signals
     */
    public function run(): void
    {
        // reset variables
        $this->errorCount = 0;
        $this->mainLoop = true;
        // block signals
        $this->pcntlSignals->block();
        // main loop
        while ($this->continueOnMainLoop()) {
            // get the time to wait based on pause or sequencer
            if ($this->getPause()) {
                $timetowait = $this->waitTime(1);
            } else {
                // get the process result
                $result = $this->executable->runOnce();
                // increase the error count based on result
                $this->errorCount = $result ? 0 : $this->errorCount + 1;
                // calculate time to wait
                $timetowait = $this->waitTime($this->sequencer->calculate($this->errorCount));
            }
            // wait
            $signo = $this->pcntlSignals->wait($timetowait);
            if ($signo > 0) {
                $this->signalHandler($signo);
            }
        }
        // unblock signals
        $this->pcntlSignals->unblock();
    }

    /**
     * Signal processor procedure:
     * 1 Send the signal to executor
     * 2 Process the signal received
     */
    protected function signalHandler(int $signo): void
    {
        // send the signal handler to the executable
        $this->executable->signalHandler($signo);
        // process signals
        if (SIGUSR1 === $signo) { // pause
            $this->setPause(true);
        } elseif (SIGUSR2 === $signo) { // unpause
            $this->setPause(false);
        } elseif (SIGHUP === $signo) { // reset error counter
            $this->resetErrorCounter();
        } elseif (SIGTERM === $signo || SIGINT === $signo || SIGQUIT === $signo) {  // terminate
            $this->terminate();
        } else {
            // If the signal is not handled create an E_USER_WARNING
            // If this happends then this function is not implementing all the signals
            trigger_error(self::class . "::signalHandler($signo) do nothing", E_USER_WARNING);
        }
    }
}
