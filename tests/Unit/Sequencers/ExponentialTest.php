<?php

declare(strict_types=1);

namespace Eclipxe\SoftDaemon\Tests\Unit\Sequencers;

use Eclipxe\SoftDaemon\Sequencer;
use Eclipxe\SoftDaemon\Sequencers\Exponential;
use PHPUnit\Framework\TestCase;

class ExponentialTest extends TestCase
{
    public function testConstructor(): void
    {
        $sequencer = new Exponential();
        $this->assertInstanceOf(Sequencer::class, $sequencer, 'Exponential sequencer does not implement Sequencer interface');
        $this->assertSame(2, $sequencer->getBase(), 'Default base is not 2');
    }

    public function testConstructorLowerThan2(): void
    {
        $bases = [-5, -1, 0, 1];
        foreach ($bases as $base) {
            $sequencer = new Exponential($base);
            $this->assertSame(2, $sequencer->getBase(), "Exponential sequencer created with base $base does not set base 2");
        }
    }

    /** @return array<array{int, int[]}> */
    public function providerCalculate(): array
    {
        return [
            [2, [0, 1, 3, 7, 15, 31]],
            [4, [0, 3, 15, 63, 10 => 1048575]],
            [10, [0, 9, 99, 999, 9999, 99999, 999999]],
        ];
    }

    /**
     * @param int $base
     * @param array<int, int> $values
     * @dataProvider providerCalculate
     */
    public function testCalculate(int $base, array $values): void
    {
        $sequencer = new Exponential($base);
        $this->assertSame($base, $sequencer->getBase(), "Default base is not $base");
        foreach ($values as $count => $value) {
            $this->assertSame($value, $sequencer->calculate($count), "Exponential($base)::calculate($count) does not match");
        }
    }
}
