<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Tests\Unit\Sequencers;

use Eclipxe\SoftDaemon\Sequencer;
use Eclipxe\SoftDaemon\Sequencers\Linear;
use PHPUnit\Framework\TestCase;

class LinearTest extends TestCase
{
    public function testConstructor(): void
    {
        $sequencer = new Linear();
        $this->assertInstanceOf(Sequencer::class, $sequencer, 'Linear sequencer does not implement Sequencer interface');
    }

    public function testCalculate(): void
    {
        $sequencer = new Linear();
        /** @noinspection PhpUnhandledExceptionInspection */
        for ($i = 0; $i < 10; $i += random_int(1, 10)) {
            $this->assertSame($i, $sequencer->calculate($i), "Linear::calculate($i) does not match");
        }
    }
}
