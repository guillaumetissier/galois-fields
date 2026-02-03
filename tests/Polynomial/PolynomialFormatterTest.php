<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Tests\Polynomial;

use Guillaumetissier\GaloisFields\GaloisField;
use Guillaumetissier\GaloisFields\Polynomial\Polynomial;
use Guillaumetissier\GaloisFields\Polynomial\PolynomialFormatter;
use Guillaumetissier\GaloisFields\Polynomial\PolynomialImmutable;
use PHPUnit\Framework\TestCase;

final class PolynomialFormatterTest extends TestCase
{
    private GaloisField $gf256;
    private GaloisField $gf7;

    protected function setUp(): void
    {
        $this->gf256 = new GaloisField(256);
        $this->gf7   = new GaloisField(7);
    }

    // -------------------------------------------------------------------------
    // toString
    // -------------------------------------------------------------------------

    public function testToStringZero(): void
    {
        $p = PolynomialImmutable::zero($this->gf256);

        $this->assertSame('0', PolynomialFormatter::toString($p));
    }

    public function testToStringConstant(): void
    {
        $p = PolynomialImmutable::constant($this->gf256, 7);

        $this->assertSame('7', PolynomialFormatter::toString($p));
    }

    public function testToStringLinear(): void
    {
        $p = PolynomialImmutable::fromCoefficients($this->gf256, [3, 7]);

        $this->assertSame('3x + 7', PolynomialFormatter::toString($p));
    }

    public function testToStringLinearMonic(): void
    {
        // x + 3 → coefficient dominant = 1, on n'écrit pas "1x"
        $p = PolynomialImmutable::fromCoefficients($this->gf256, [1, 3]);

        $this->assertSame('x + 3', PolynomialFormatter::toString($p));
    }

    public function testToStringQuadratic(): void
    {
        $p = PolynomialImmutable::fromCoefficients($this->gf256, [5, 3, 7]);

        $this->assertSame('5x^2 + 3x + 7', PolynomialFormatter::toString($p));
    }

    public function testToStringMonic(): void
    {
        // x² + 1 → pas de "1x^2"
        $p = PolynomialImmutable::fromCoefficients($this->gf256, [1, 0, 1]);

        $this->assertSame('x^2 + 1', PolynomialFormatter::toString($p));
    }

    public function testToStringSkipsZeroCoefficients(): void
    {
        // x³ + 5x → les termes x² et x⁰ sont 0
        $p = PolynomialImmutable::fromCoefficients($this->gf256, [1, 0, 5, 0]);

        $this->assertSame('x^3 + 5x', PolynomialFormatter::toString($p));
    }

    public function testToStringMonomial(): void
    {
        $p = PolynomialImmutable::monomial($this->gf256, 4, 3); // 3x⁴

        $this->assertSame('3x^4', PolynomialFormatter::toString($p));
    }

    public function testToStringMonomialMonic(): void
    {
        $p = PolynomialImmutable::monomial($this->gf256, 4); // x⁴

        $this->assertSame('x^4', PolynomialFormatter::toString($p));
    }

    public function testToAlphaStringZero(): void
    {
        $p = PolynomialImmutable::zero($this->gf256);

        $this->assertSame('0', PolynomialFormatter::toAlphaString($p));
    }

    public function testToAlphaStringConstantOne(): void
    {
        $p = PolynomialImmutable::constant($this->gf256, 1);

        $this->assertSame('α^0', PolynomialFormatter::toAlphaString($p));
    }

    public function testToAlphaStringSimple(): void
    {
        // [2, 4, 1] → α^1 x² + α^2 x + 1
        $p = PolynomialImmutable::fromCoefficients($this->gf256, [2, 4, 1]);

        $this->assertSame('α^1x^2 + α^2x + α^0', PolynomialFormatter::toAlphaString($p));
    }

    public function testToAlphaStringSkipsZeros(): void
    {
        // [2, 0, 4] → α^1 x² + α^2
        $p = PolynomialImmutable::fromCoefficients($this->gf256, [2, 0, 4]);

        $this->assertSame('α^1x^2 + α^2', PolynomialFormatter::toAlphaString($p));
    }

    public function testToAlphaStringOnPrimeFieldThrows(): void
    {
        $p = PolynomialImmutable::fromCoefficients($this->gf7, [3, 2]);

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('toAlphaString() requires a binary extension field');

        PolynomialFormatter::toAlphaString($p);
    }

    public function testToStringAcceptsMutablePolynomial(): void
    {
        $p = Polynomial::fromCoefficients($this->gf256, [5, 3, 7]);

        $this->assertSame('5x^2 + 3x + 7', PolynomialFormatter::toString($p));
    }

    public function testToAlphaStringAcceptsMutablePolynomial(): void
    {
        $p = Polynomial::fromCoefficients($this->gf256, [2, 4, 1]);

        $this->assertSame('α^1x^2 + α^2x + α^0', PolynomialFormatter::toAlphaString($p));
    }
}
