<?php

namespace SoftDaemonTests;

use SoftDaemon\SoftDaemon;

class SoftDaemonTest extends \PHPUnit_Framework_TestCase
{

    public function createMockExecutable()
    {
        return new MockExecutable();
    }

    public function testDefaultConstructor()
    {
        $sd = new SoftDaemon($this->createMockExecutable());
        $this->assertSame(SoftDaemon::DEFAULT_MAXWAIT, $sd->getMaxWait(), 'default maxwait must be the same as constant');
        $this->assertSame(SoftDaemon::DEFAULT_MINWAIT, $sd->getMinWait(), 'default minwait must be the same as constant');
        $this->assertFalse($sd->getPause(), 'default pause status must be false');
        $this->assertSame(0, $sd->getErrorCounter(), 'Must be no errors on SoftDaemon creation');
    }

    public function testPause()
    {
        $sd = new SoftDaemon($this->createMockExecutable());
        $sd->setPause(false);
        $this->assertFalse($sd->getPause(), 'Pause must be false after setting that value (1 round)');
        $sd->setPause(true);
        $this->assertTrue($sd->getPause(), 'Pause must be true after setting that value');
        $sd->setPause(false);
        $this->assertFalse($sd->getPause(), 'Pause must be false after setting that value (2 round)');
    }

    public function testMaxWait()
    {
        $sd = new SoftDaemon($this->createMockExecutable());
        $sd->setMaxWait(100);
        $this->assertSame(100, $sd->getMaxWait(), 'MaxWait was not get correct with a valid number');
        $sd->setMaxWait(-5);
        $this->assertSame(1, $sd->getMaxWait(), 'MaxWait was not set to 1 with a negative number');
        $invalid = ['not a number', null, new \stdClass()];
        foreach($invalid as $number) {
            $sd->setMaxWait($number);
            $this->assertSame(SoftDaemon::DEFAULT_MAXWAIT, $sd->getMaxWait(), "MaxWait was not get as 1 with an invalid number");
        }
    }

    public function testMinWait()
    {
        $sd = new SoftDaemon($this->createMockExecutable());
        $sd->setMinWait(10);
        $this->assertSame(10, $sd->getMinWait(), 'MinWait was not get correct with a valid number');
        $sd->setMinWait(-5);
        $this->assertSame(0, $sd->getMinWait(), 'MinWait was not set to 1 with a negative number');
        $invalid = ['not a number', null, new \stdClass()];
        foreach($invalid as $number) {
            $sd->setMinWait($number);
            $this->assertSame(SoftDaemon::DEFAULT_MINWAIT, $sd->getMinWait(), "MaxWait was not get as 1 with an invalid number");
        }
    }

    public function testWaitTime()
    {
        $sd = new MockSoftDaemon($this->createMockExecutable());
        $sd->setMinWait(10);
        $sd->setMaxWait(100);
        $this->assertSame(10, $sd->exposeWaitTime(1), 'wait time is not returning minwait');
        $this->assertSame(100, $sd->exposeWaitTime(200), 'wait time is not returning maxwait');
        $this->assertSame(50, $sd->exposeWaitTime(50), 'wait time is not value between minwait and maxwait');
    }

    public function testgetErrorCounter()
    {
        $sd = new MockSoftDaemon($this->createMockExecutable());
        $sd->setErrorCounter(10);
        $this->assertSame(10, $sd->getErrorCounter(), 'Mocked error counter does not return fixed value');
        $sd->resetErrorCounter();
        $this->assertSame(0, $sd->getErrorCounter(), 'Error counter was not zero after reset error counter');
    }

    public function testSignalHandler()
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

    /**
     * @expectedException \PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage SoftDaemon\SoftDaemon::signalHandler(-128) do nothing
     */
    public function testSignalHandlerBadSignal()
    {
        $sd = new MockSoftDaemon($this->createMockExecutable());
        $sd->exposeSignalHandler(-128);
    }

    public function testRun()
    {
        $msg_sd = [
            'Signal 1 received',
            'Signal 10 received',
            'Signal 12 received',
            'Signal 15 received',
            'Terminate called'
        ];
        $msg_ex = [
            'Run 1',
            'Run 2',
            'Run 3',
            'Run 4',
            'Executable signal 1 on 4',
            'Run 5',
            'Run 6',
            'Executable signal 10 on 6',
            'Executable signal 12 on 6',
            'Run 7',
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
        $sd->run();
        $this->assertEquals($msg_sd, $sd->messages, 'Execution messages at SoftDaemon does not match');
        $this->assertEquals($msg_ex, $executable->messages, 'Execution messages at Executable does not match');
        $this->assertEquals($msg_pc, $sd->exposePcntlSignals()->messages, 'Execution messages at PcntlSignals does not match');
    }

}
