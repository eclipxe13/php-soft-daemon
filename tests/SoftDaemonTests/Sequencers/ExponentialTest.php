<?php

namespace SoftDaemonTests\Sequencers;

use SoftDaemon\Sequencers\Exponential;

class ExponentialTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $sequencer = new Exponential();
        $this->assertInstanceOf('\SoftDaemon\Sequencer', $sequencer, 'Exponential sequencer does not implement Sequencer interface');
        $this->assertSame(2, $sequencer->getBase(), 'Default base is not 2');
    }

    public function testConstructorLowerThan2()
    {
        $bases = [-5, -1, 0, 1, 1.2, null, 'not an integer', '-4'];
        foreach($bases as $base) {
            $sequencer = new Exponential($base);
            $this->assertSame(2, $sequencer->getBase(), "Exponential sequencer created with base $base does not set base 2");
        }
    }

    public function providerCalculate()
    {
        return [
            [2, [0, 1, 3, 7, 15, 31]],
            [4, [0, 3, 15, 63, 10 => 1048575]],
            [10, [0, 9, 99, 999, 9999, 99999, 999999]],
        ];
    }

    /**
     * @dataProvider providerCalculate
     */
    public function testCalculate($base, $values)
    {
        $sequencer = new Exponential($base);
        $this->assertSame($base, $sequencer->getBase(), "Default base is not $base");
        foreach($values as $count => $value) {
            $this->assertSame($value, $sequencer->calculate($count), "Exponential($base)::calculate($count) does not match");
        }
    }
}
