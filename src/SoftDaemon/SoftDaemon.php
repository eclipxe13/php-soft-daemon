<?php

namespace SoftDaemon;

use SoftDaemon\Sequencers\Fixed as FixedSequencer;
use SoftDaemon\Internal\PcntlSignals;

/**
 * @package SoftDaemon
 */
class SoftDaemon
{
    /** maximum wait in seconds (1 hour) */
    const DEFAULT_MAXWAIT = 3600;

    /** minimum wait in seconds (no wait) */
    const DEFAULT_MINWAIT = 0;

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

    /** @var boolean pause state of the object */
    protected $pause = false;

    /** @var boolean flag to control main loop */
    protected $mainloop = true;

    /** @var PcntlSignals Native php functions (isolated for testing) */
    protected $pcntlsignals;

    /** @var array Set of signals to block and wait for */
    protected $signals = [SIGHUP, SIGTERM, SIGINT, SIGQUIT, SIGUSR1, SIGUSR2];

    /**
     * @param \SoftDaemon\Executable $executable Executable object
     * @param \SoftDaemon\Sequencer $sequencer Sequencer object If null then a FixedSequencer(1) will be used
     * @param int $maxwait Maximum seconds to wait before call again the executable object (min: 1)
     * @param int $minwait Minimum seconds to wait before call again the executable object (min: 0)
     */
    public function __construct(Executable $executable, Sequencer $sequencer = null, $maxwait = self::DEFAULT_MAXWAIT, $minwait = self::DEFAULT_MINWAIT)
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
     * @param int $maxwait
     */
    public function setMaxWait($maxwait)
    {
        $this->maxwait = max(1, is_numeric($maxwait) ? (int) $maxwait : self::DEFAULT_MAXWAIT);
    }

    /**
     * Get the maxwait seconds
     * @return int
     */
    public function getMaxWait()
    {
        return $this->maxwait;
    }

    /**
     * Set the minwait seconds, the SoftDaemon will not wait less than this quantity of seconds
     * Any value lower than 0 is fixed to 0, if not numeric uses default 0
     * @param int $minwait
     */
    public function setMinWait($minwait)
    {
        $this->minwait = max(0, is_numeric($minwait) ? (int) $minwait : self::DEFAULT_MINWAIT);
    }

    /**
     * Get the minwait seconds
     * @param int $minwait
     */
    public function getMinWait()
    {
        return $this->minwait;
    }

    /**
     * Reset the error counter to zero
     */
    public function resetErrorCounter()
    {
        $this->errorcount = 0;
    }

    /**
     * Will exit the main loop on the next iteration
     */
    public function terminate()
    {
        $this->mainloop = false;
    }

    /**
     * Count of consecutive times the executable return error
     * This value can only be set to zero using resetErrorCounter
     * @return int
     */
    public function getErrorCounter()
    {
        return $this->errorcount;
    }

    /**
     * Set the pause status, if on pause then main loop will only waiting 1 second until another signal is received
     * The executor is not call when the SoftDaemon is on pause
     * The time to wait on pause is 1 second, but this is fixed to minwait and maxwait
     * @param bool $pause
     */
    public function setPause($pause)
    {
        $this->pause = (bool) $pause;
    }

    /**
     * Get the pause status
     * @return bool
     */
    public function getPause()
    {
        return $this->pause;
    }

    /**
     * Fix the wait time to force minwait and maxwait
     * @param int $seconds
     * @return int
     */
    protected function waitTime($seconds)
    {
        return max($this->minwait, min($this->maxwait, $seconds));
    }

    /**
     * Run the executor expecting signals
     */
    public function run()
    {
        // reset variables
        $this->errorcount = 0;
        $this->mainloop = true;
        // block signals
        $this->pcntlsignals->block();
        // main loop
        while ($this->mainloop) {
            // get the time to wait based on pause or sequencer
            if ($this->getPause()) {
                $timetowait = $this->waitTime(1);
            } else {
                // get the process result
                $result = $this->executable->runOnce();
                // increase the error count based on result
                $this->errorcount = ($result) ? 0 : $this->errorcount + 1;
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
    protected function signalHandler($signo)
    {
        // send the signal handler to the executable
        $this->executable->signalHandler($signo);
        // process signals
        if ($signo === SIGUSR1) { // pause
            $this->setPause(true);
        } elseif ($signo === SIGUSR2) { // unpause
            $this->setPause(false);
        } elseif ($signo === SIGHUP) { // reset error counter
            $this->resetErrorCounter();
        } elseif ($signo === SIGTERM || $signo === SIGINT || $signo === SIGQUIT) {  // terminate
            $this->terminate();
        } else {
            // If the signal is not handled create a E_USER_WARNING
            // If this happends then this function is not implementing all the signals
            \trigger_error(__CLASS__ . "::signalHandler($signo) do nothing", E_USER_WARNING);
        }
    }
}
