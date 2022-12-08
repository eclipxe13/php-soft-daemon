<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon;

use Eclipxe\SoftDaemon\Internal\PcntlSignals;
use Eclipxe\SoftDaemon\Sequencers\Fixed as FixedSequencer;

class SoftDaemon
{
    /** maximum wait in seconds (1 hour) */
    public const DEFAULT_MAXWAIT = 3600;

    /** minimum wait in seconds (no wait) */
    public const DEFAULT_MINWAIT = 0;

    /** @var Executable **/
    protected $executable;

    /** @var Sequencer **/
    protected $sequencer;

    /** @var int minimal wait */
    protected $minwait;

    /** @var int maximum wait */
    protected $maxwait;

    /** @var int count of consecutive times the executable return error */
    protected $errorcount = 0;

    /** @var bool pause state of the object */
    protected $pause = false;

    /** @var bool flag to control main loop */
    protected $mainloop = false;

    /** @var PcntlSignals Native php functions (isolated for testing) */
    protected $pcntlsignals;

    /** @var int[] Set of signals to block and wait for */
    protected $signals = [SIGHUP, SIGTERM, SIGINT, SIGQUIT, SIGUSR1, SIGUSR2];

    /**
     * @param Executable $executable Executable object
     * @param Sequencer|null $sequencer Sequencer object If null then a FixedSequencer(1) will be used
     * @param int $maxwait Maximum seconds to wait before call again the executable object (min: 1)
     * @param int $minwait Minimum seconds to wait before call again the executable object (min: 0)
     */
    public function __construct(Executable $executable, Sequencer $sequencer = null, int $maxwait = self::DEFAULT_MAXWAIT, int $minwait = self::DEFAULT_MINWAIT)
    {
        $this->executable = $executable;
        if (null === $sequencer) {
            $sequencer = new FixedSequencer(1);
        }
        $this->sequencer = $sequencer;
        $this->setMaxWait($maxwait);
        $this->setMinWait($minwait);
        $this->pcntlsignals = new PcntlSignals($this->signals);
    }

    /**
     * Set the maxwait seconds, the SoftDaemon will not wait more than this quantity of seconds
     * Any value lower than 1 is fixed to 1, if not numeric uses default self::DEFAULT_MAXWAIT
     *
     * @param int $maxwait
     */
    public function setMaxWait(int $maxwait): void
    {
        $this->maxwait = max(1, $maxwait);
    }

    /**
     * Get the maxwait seconds
     * @return int
     */
    public function getMaxWait(): int
    {
        return $this->maxwait;
    }

    /**
     * Set the minwait seconds, the SoftDaemon will not wait less than this quantity of seconds
     * Any value lower than 0 is fixed to 0, if not numeric uses default 0
     *
     * @param int $minwait
     */
    public function setMinWait(int $minwait): void
    {
        $this->minwait = max(0, $minwait);
    }

    /**
     * Get the minwait seconds
     * @return int $minwait
     */
    public function getMinWait(): int
    {
        return $this->minwait;
    }

    /**
     * Reset the error counter to zero
     */
    public function resetErrorCounter(): void
    {
        $this->errorcount = 0;
    }

    /**
     * Will exit the main loop on the next iteration
     */
    public function terminate(): void
    {
        $this->mainloop = false;
    }

    /**
     * Internally check if must continue on main loop
     */
    protected function continueOnMainLoop(): bool
    {
        return $this->mainloop;
    }

    /**
     * Count of consecutive times the executable return error
     * This value can only be set to zero using resetErrorCounter
     * @return int
     */
    public function getErrorCounter(): int
    {
        return $this->errorcount;
    }

    /**
     * Set the pause status, if on pause then main loop will only wait 1 second until another signal is received
     * The executor is not called when the SoftDaemon is on pause
     * The time to wait on pause is 1 second, but this is fixed to minwait and maxwait
     *
     * @param bool $pause
     */
    public function setPause(bool $pause): void
    {
        $this->pause = $pause;
    }

    /**
     * Get the pause status
     * @return bool
     */
    public function getPause(): bool
    {
        return $this->pause;
    }

    /**
     * Fix the wait time to force minwait and maxwait
     *
     * @param int $seconds
     * @return int
     */
    protected function waitTime(int $seconds): int
    {
        return max($this->minwait, min($this->maxwait, $seconds));
    }

    /**
     * Run the executor expecting signals
     */
    public function run(): void
    {
        // reset variables
        $this->errorcount = 0;
        $this->mainloop = true;
        // block signals
        $this->pcntlsignals->block();
        // main loop
        while ($this->continueOnMainLoop()) {
            // get the time to wait based on pause or sequencer
            if ($this->getPause()) {
                $timetowait = $this->waitTime(1);
            } else {
                // get the process result
                $result = $this->executable->runOnce();
                // increase the error count based on result
                if ($result) {
                    $this->errorcount = 0;
                } else {
                    $this->errorcount = $this->errorcount + 1;
                }
                // calculate time to wait
                $timetowait = $this->waitTime($this->sequencer->calculate($this->errorcount));
            }
            // wait
            $signo = $this->pcntlsignals->wait($timetowait);
            if ($signo > 0) {
                $this->signalHandler($signo);
            }
        }
        // unblock signals
        $this->pcntlsignals->unblock();
    }

    /**
     * Signal processor procedure:
     * 1 Send the signal to executor
     * 2 Process the signal received
     *
     * @param int $signo
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
            trigger_error(__CLASS__ . "::signalHandler($signo) do nothing", E_USER_WARNING);
        }
    }
}
