<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Tests\Unit\Sequencers;

use Eclipxe\SoftDaemon\Sequencer;
use Eclipxe\SoftDaemon\Sequencers\Fixed;
use PHPUnit\Framework\TestCase;

class FixedTest extends TestCase
{
    public function testConstructor(): void
    {
        $sequencer = new Fixed();
        $this->assertInstanceOf(Sequencer::class, $sequencer, 'Fixed sequencer does not implement Sequencer interface');
        $this->assertSame(1, $sequencer->getSeconds(), 'Default seconds must be 1');
    }

    /**
     * @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection
     * @return array<mixed>
     */
    public function providerConstructorSeconds(): array
    {
        return [
            [-5, 0],
            [0, 0],
            [1, 1],
            [5, 5],
        ];
    }

    /**
     * @dataProvider providerConstructorSeconds
     */
    public function testConstructorSeconds(int $value, int $expected): void
    {
        $sequencer = new Fixed($value);
        $this->assertSame($expected, $sequencer->getSeconds(), "Fixed sequencer created with value '$value' does not return expected $expected");
    }

    public function testCalculate(): void
    {
        $fixed = 5;
        $sequencer = new Fixed($fixed);
        /** @noinspection PhpUnhandledExceptionInspection */
        for ($i = 0; $i < 10; $i += random_int(1, 10)) {
            $this->assertSame($fixed, $sequencer->calculate($i), "Fixed::calculate($i) does not match with fixed value $fixed");
        }
    }
}
