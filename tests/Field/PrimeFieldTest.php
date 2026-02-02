<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Tests\Field;

use Guillaumetissier\GaloisFields\GaloisField;
use PHPUnit\Framework\TestCase;

final class PrimeFieldTest extends TestCase
{
    public function testGF7Properties(): void
    {
        $gf = new GaloisField(7);

        $this->assertSame(7, $gf->getOrder());
        $this->assertSame(7, $gf->getCharacteristic());
        $this->assertSame(1, $gf->getDegree());
    }

    public function testGF7Operations(): void
    {
        $gf = new GaloisField(7);

        // Addition
        $this->assertSame(5, $gf->add(3, 2));
        $this->assertSame(1, $gf->add(5, 3)); // (5 + 3) mod 7 = 1

        // Multiplication
        $this->assertSame(6, $gf->multiply(2, 3));
        $this->assertSame(1, $gf->multiply(5, 3)); // (5 * 3) mod 7 = 1

        // Subtraction
        $this->assertSame(1, $gf->subtract(3, 2));
        $this->assertSame(5, $gf->subtract(2, 4)); // (2 - 4) mod 7 = 5
    }

    public function testGF7Inverse(): void
    {
        $gf = new GaloisField(7);

        // Test all non-zero elements
        for ($x = 1; $x < 7; $x++) {
            $inv = $gf->inverse($x);
            $this->assertSame(1, $gf->multiply($x, $inv), "Failed for x=$x");
        }
    }

    public function testGF11Operations(): void
    {
        $gf = new GaloisField(11);

        $this->assertSame(11, $gf->getOrder());
        $this->assertSame(8, $gf->add(5, 3));
        $this->assertSame(1, $gf->add(7, 5)); // (7 + 5) mod 11 = 12 mod 11 = 1
    }

    public function testGF13Multiplication(): void
    {
        $gf = new GaloisField(13);

        $this->assertSame(12, $gf->multiply(3, 4));
        $this->assertSame(1, $gf->multiply(5, 8)); // (5 * 8) mod 13 = 40 mod 13 = 1
    }

    public function testDivision(): void
    {
        $gf = new GaloisField(11);

        $a = 7;
        $b = 3;

        $quotient = $gf->divide($a, $b);

        // Verify: quotient * b = a (mod 11)
        $this->assertSame($a, $gf->multiply($quotient, $b));
    }

    public function testPower(): void
    {
        $gf = new GaloisField(7);

        $this->assertSame(1, $gf->power(3, 0));
        $this->assertSame(3, $gf->power(3, 1));
        $this->assertSame(2, $gf->power(3, 2)); // 9 mod 7 = 2
        $this->assertSame(6, $gf->power(3, 3)); // 27 mod 7 = 6
    }

    public function testFermatLittleTheorem(): void
    {
        $gf = new GaloisField(7);

        // For prime p and any a not divisible by p: a^(p-1) ≡ 1 (mod p)
        for ($a = 1; $a < 7; $a++) {
            $this->assertSame(1, $gf->power($a, 6), "Failed for a=$a");
        }
    }
}
