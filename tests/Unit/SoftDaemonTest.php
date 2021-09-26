<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Tests\Unit;

use Eclipxe\SoftDaemon\SoftDaemon;
use PHPUnit\Framework\TestCase;

class SoftDaemonTest extends TestCase
{
    public function createMockExecutable(): MockExecutable
    {
        return new MockExecutable();
    }

    public function testDefaultConstructor(): void
    {
        $sd = new SoftDaemon($this->createMockExecutable());
        $this->assertSame(SoftDaemon::DEFAULT_MAXWAIT, $sd->getMaxWait(), 'default maxwait must be the same as constant');
        $this->assertSame(SoftDaemon::DEFAULT_MINWAIT, $sd->getMinWait(), 'default minwait must be the same as constant');
        $this->assertFalse($sd->getPause(), 'default pause status must be false');
        $this->assertSame(0, $sd->getErrorCounter(), 'Must be no errors on SoftDaemon creation');
    }

    public function testPause(): void
    {
        $sd = new SoftDaemon($this->createMockExecutable());
        $sd->setPause(false);
        $this->assertFalse($sd->getPause(), 'Pause must be false after setting that value (1 round)');
        $sd->setPause(true);
        $this->assertTrue($sd->getPause(), 'Pause must be true after setting that value');
        $sd->setPause(false);
        $this->assertFalse($sd->getPause(), 'Pause must be false after setting that value (2 round)');
    }

    public function testMaxWait(): void
    {
        $sd = new SoftDaemon($this->createMockExecutable());
        $sd->setMaxWait(100);
        $this->assertSame(100, $sd->getMaxWait(), 'MaxWait was not get correct with a valid number');
        $sd->setMaxWait(-5);
        $this->assertSame(1, $sd->getMaxWait(), 'MaxWait was not set to 1 with a negative number');
    }

    public function testMinWait(): void
    {
        $sd = new SoftDaemon($this->createMockExecutable());
        $sd->setMinWait(10);
        $this->assertSame(10, $sd->getMinWait(), 'MinWait was not get correct with a valid number');
        $sd->setMinWait(-5);
        $this->assertSame(0, $sd->getMinWait(), 'MinWait was not set to 1 with a negative number');
    }

    public function testWaitTime(): void
    {
        $sd = new MockSoftDaemon($this->createMockExecutable());
        $sd->setMinWait(10);
        $sd->setMaxWait(100);
        $this->assertSame(10, $sd->exposeWaitTime(1), 'wait time is not returning minwait');
        $this->assertSame(100, $sd->exposeWaitTime(200), 'wait time is not returning maxwait');
        $this->assertSame(50, $sd->exposeWaitTime(50), 'wait time is not value between minwait and maxwait');
    }

    public function testGetErrorCounter(): void
    {
        $sd = new MockSoftDaemon($this->createMockExecutable());
        $sd->setErrorCounter(10);
        $this->assertSame(10, $sd->getErrorCounter(), 'Mocked error counter does not return fixed value');
        $sd->resetErrorCounter();
        $this->assertSame(0, $sd->getErrorCounter(), 'Error counter was not zero after reset error counter');
    }

    public function testErrorCounterAfterRun(): void
    {
        // prepare to run 8 times
        $pcntlSignals = new MockPcntlSignals([SIGTERM]);
        $pcntlSignals->returnSignals = [SIGTERM, 0, 0, 0, SIGTERM, 0, 0, SIGTERM];
        $executable = $this->createMockExecutable();
        $executable->runValues = [
            false, // error count will be 1 [SIGTERM]
            true,  // error count will be reset to 0
            false, // error count will be 1
            false, // error count will be 2
            false, // error count will be 3 [SIGTERM]
            true,  // error count will be reset to 0
            false, // error count will be 1
            false, // error count will be 2 [SIGTERM]
        ];

        $sd = new MockSoftDaemon($executable);
        $sd->setPcntlSignals($pcntlSignals);

        // define error counter state before first run
        $sd->setErrorCounter(10);

        // first run fail, this is why run reset to 0 when start
        $sd->run();
        $this->assertSame(1, $sd->getErrorCounter());

        // check that run reset counter
        $sd->run();
        $this->assertSame(3, $sd->getErrorCounter());

        // check that run reset counter (again)
        $sd->run();
        $this->assertSame(2, $sd->getErrorCounter());
    }

    public function testSignalHandler(): void
    {
        $executable = $this->createMockExecutable();
        $sd = new MockSoftDaemon($executable);
        $sd->exposeSignalHandler(SIGUSR1);
        $this->assertTrue($sd->getPause(), 'SoftDaemon does not enter on pause statuc after SIGUSR1');
        $sd->exposeSignalHandler(SIGUSR1);
        $this->assertTrue($sd->getPause(), 'SoftDaemon does maintain pause status after double SIGUSR1');
        $sd->exposeSignalHandler(SIGUSR2);
        $this->assertFalse($sd->getPause(), 'SoftDaemon does exit from pause status after SIGUSR2');
        $sd->exposeSignalHandler(SIGUSR2);
        $this->assertFalse($sd->getPause(), 'SoftDaemon does maintain pause status (off) after double SIGUSR2');
        $sd->setErrorCounter(10);
        $sd->exposeSignalHandler(SIGHUP);
        $this->assertSame(0, $sd->getErrorCounter(), 'Error counter was not zero after reset error counter by SIGHUP');
        $sd->messages = [];
        $sd->exposeSignalHandler(SIGTERM);
        $this->assertSame(['Signal 15 received', 'Terminate called'], $sd->messages, 'Do not detect terminate call after SIGTERM');
        $sd->messages = [];
        $sd->exposeSignalHandler(SIGINT);
        $this->assertSame(['Signal 2 received', 'Terminate called'], $sd->messages, 'Do not detect terminate call after SIGINT');
        $sd->messages = [];
        $sd->exposeSignalHandler(SIGQUIT);
        $this->assertSame(['Signal 3 received', 'Terminate called'], $sd->messages, 'Do not detect terminate call after SIGQUIT');
    }

    public function testSignalHandlerBadSignal(): void
    {
        $sd = new MockSoftDaemon($this->createMockExecutable());
        $this->expectWarning();
        $this->expectWarningMessage('Eclipxe\SoftDaemon\SoftDaemon::signalHandler(-128) do nothing');
        $sd->exposeSignalHandler(-128);
    }

    public function testRun(): void
    {
        $msg_sd = [
            'Signal 1 received',
            'Signal 10 received',
            'Signal 12 received',
            'Signal 15 received',
            'Terminate called',
        ];
        $msg_ex = [
            'Run 1 will return false',
            'Run 2 will return false',
            'Run 3 will return false',
            'Run 4 will return false',
            'Executable signal 1 on 4',
            'Run 5 will return false',
            'Run 6 will return false',
            'Executable signal 10 on 6',
            'Executable signal 12 on 6',
            'Run 7 will return false',
            'Executable signal 15 on 7',
        ];
        $msg_pc = [
            'block [1, 15, 2, 3, 10, 12]',
            'Wait for 1 seconds, return 0',
            'Wait for 1 seconds, return 0',
            'Wait for 1 seconds, return 0',
            'Wait for 1 seconds, return 1',
            'Wait for 1 seconds, return 0',
            'Wait for 1 seconds, return 10',
            'Wait for 1 seconds, return 12',
            'Wait for 1 seconds, return 15',
            'unblock [1, 15, 2, 3, 10, 12]',
        ];
        $executable = $this->createMockExecutable();
        $sd = new MockSoftDaemon($executable);
        /** @var MockPcntlSignals $pcntlSignals */
        $pcntlSignals = $sd->exposePcntlSignals();
        $sd->run();
        $this->assertEquals($msg_sd, $sd->messages, 'Execution messages at SoftDaemon does not match');
        $this->assertEquals($msg_ex, $executable->messages, 'Execution messages at Executable does not match');
        $this->assertEquals($msg_pc, $pcntlSignals->messages, 'Execution messages at PcntlSignals does not match');
    }
}
