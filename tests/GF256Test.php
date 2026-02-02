<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Tests;

use Guillaumetissier\GaloisFields\GaloisField;
use PHPUnit\Framework\TestCase;

final class GF256Test extends TestCase
{
    private GaloisField $gf;

    protected function setUp(): void
    {
        $this->gf = new GaloisField(256);
    }

    public function testFieldProperties(): void
    {
        $this->assertSame(256, $this->gf->getOrder());
        $this->assertSame(2, $this->gf->getCharacteristic());
        $this->assertSame(8, $this->gf->getDegree());
    }

    public function testAddition(): void
    {
        // In GF(2^n), addition is XOR
        $this->assertSame(0, $this->gf->add(5, 5));
        $this->assertSame(6, $this->gf->add(5, 3)); // 0b101 ^ 0b011 = 0b110
        $this->assertSame(255, $this->gf->add(170, 85)); // 0b10101010 ^ 0b01010101 = 0b11111111
    }

    public function testSubtraction(): void
    {
        // In characteristic 2, subtraction equals addition
        $this->assertSame(6, $this->gf->subtract(5, 3));
        $this->assertSame(6, $this->gf->add(5, 3));
    }

    public function testMultiplication(): void
    {
        $this->assertSame(0, $this->gf->multiply(0, 123));
        $this->assertSame(0, $this->gf->multiply(123, 0));
        $this->assertSame(123, $this->gf->multiply(1, 123));
        $this->assertSame(123, $this->gf->multiply(123, 1));

        // Test actual multiplication
        $result = $this->gf->multiply(53, 45);
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(256, $result);
    }

    public function testMultiplicationIsCommutative(): void
    {
        $this->assertSame(
            $this->gf->multiply(53, 45),
            $this->gf->multiply(45, 53)
        );
    }

    public function testMultiplicationIsAssociative(): void
    {
        $a = 23;
        $b = 45;
        $c = 67;

        $this->assertSame(
            $this->gf->multiply($this->gf->multiply($a, $b), $c),
            $this->gf->multiply($a, $this->gf->multiply($b, $c))
        );
    }

    public function testDivision(): void
    {
        $a = 200;
        $b = 50;

        $quotient = $this->gf->divide($a, $b);

        // Verify: quotient * b = a
        $this->assertSame($a, $this->gf->multiply($quotient, $b));
    }

    public function testDivisionByZeroThrowsException(): void
    {
        $this->expectException(\DivisionByZeroError::class);
        $this->gf->divide(123, 0);
    }

    public function testInverse(): void
    {
        // Test that x * inverse(x) = 1 for all non-zero x
        for ($x = 1; $x < 10; $x++) {
            $inv = $this->gf->inverse($x);
            $this->assertSame(1, $this->gf->multiply($x, $inv), "Failed for x=$x");
        }
    }

    public function testInverseOfZeroThrowsException(): void
    {
        $this->expectException(\DivisionByZeroError::class);
        $this->gf->inverse(0);
    }

    public function testPower(): void
    {
        $this->assertSame(1, $this->gf->power(5, 0));
        $this->assertSame(5, $this->gf->power(5, 1));

        // Test that power works correctly
        $base = 3;
        $squared = $this->gf->multiply($base, $base);
        $this->assertSame($squared, $this->gf->power($base, 2));

        $cubed = $this->gf->multiply($squared, $base);
        $this->assertSame($cubed, $this->gf->power($base, 3));
    }

    public function testDistributivity(): void
    {
        $a = 23;
        $b = 45;
        $c = 67;

        // Test: a * (b + c) = (a * b) + (a * c)
        $left = $this->gf->multiply($a, $this->gf->add($b, $c));
        $right = $this->gf->add(
            $this->gf->multiply($a, $b),
            $this->gf->multiply($a, $c)
        );

        $this->assertSame($left, $right);
    }

    public function testValidElements(): void
    {
        $this->assertTrue($this->gf->isValidElement(0));
        $this->assertTrue($this->gf->isValidElement(128));
        $this->assertTrue($this->gf->isValidElement(255));
        $this->assertFalse($this->gf->isValidElement(256));
        $this->assertFalse($this->gf->isValidElement(-1));
    }

    public function testMultiplicativeGroupOrder(): void
    {
        // In GF(256), the multiplicative group has order 255
        // So for any non-zero element x: x^255 = 1
        $x = 7;
        $this->assertSame(1, $this->gf->power($x, 255));
    }
}
