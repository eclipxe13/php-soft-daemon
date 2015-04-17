<?php

namespace SoftDaemonTests\Sequencers;

use SoftDaemon\Sequencers\Linear;

class LinearTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $sequencer = new Linear();
        $this->assertInstanceOf('\SoftDaemon\Sequencer', $sequencer, 'Linear sequencer does not implement Sequencer interface');
    }

    public function testCalculate()
    {
        $sequencer = new Linear();
        for($i = 0 ; $i < 10 ; $i += rand(1, 10)) {
            $this->assertSame($i, $sequencer->calculate($i), "Linear::calculate($i) does not match");
        }
    }


}
