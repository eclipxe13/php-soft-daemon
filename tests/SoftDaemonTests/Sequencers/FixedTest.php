<?php

namespace SoftDaemonTests\Sequencers;

use SoftDaemon\Sequencers\Fixed;

class FixedTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $sequencer = new Fixed();
        $this->assertInstanceOf('\SoftDaemon\Sequencer', $sequencer, 'Fixed sequencer does not implement Sequencer interface');
        $this->assertSame(1, $sequencer->getSeconds(), 'Default seconds must be 1');
    }

    public function providerConstructorSeconds()
    {
        return [
            [-5, 0],
            [0, 0],
            [1, 1],
            [1.2, 1],
            [null, 1],
            ['not an integer', 1],
            ['4', 4],
        ];
    }

    /**
     * @dataProvider providerConstructorSeconds
     */
    public function testConstructorSeconds($value, $expected)
    {
        $sequencer = new Fixed($value);
        $this->assertSame($expected, $sequencer->getSeconds(), "Fixed sequencer created with value '$value' does not return expected $expected");
    }

    public function testCalculate()
    {
        $fixed = 5;
        $sequencer = new Fixed($fixed);
        for($i = 0 ; $i < 10 ; $i += rand(1, 10)) {
            $this->assertSame($fixed, $sequencer->calculate($i), "Fixed::calculate($i) does not match with fixed value $fixed");
        }
    }


}
