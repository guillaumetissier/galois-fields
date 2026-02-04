<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Tests\Polynomial;

use Guillaumetissier\GaloisFields\GaloisField;
use Guillaumetissier\GaloisFields\Polynomial\PolynomialImmutable;
use PHPUnit\Framework\TestCase;

final class PolynomialImmutableTest extends TestCase
{
    private GaloisField $gf256;
    private GaloisField $gf7;

    protected function setUp(): void
    {
        $this->gf256 = new GaloisField(256);
        $this->gf7   = new GaloisField(7);
    }

    // -------------------------------------------------------------------------
    // Factories
    // -------------------------------------------------------------------------

    public function testFromCoefficients(): void
    {
        $p = PolynomialImmutable::fromCoefficients($this->gf7, [3, 2, 1]);

        $this->assertSame(2, $p->degree());
        $this->assertSame(3, $p->leadingCoefficient());
        $this->assertSame(1, $p->coefficientAt(0));
    }

    public function testZero(): void
    {
        $p = PolynomialImmutable::zero($this->gf256);

        $this->assertTrue($p->isZero());
        $this->assertSame(-1, $p->degree());
    }

    public function testOne(): void
    {
        $p = PolynomialImmutable::one($this->gf256);

        $this->assertFalse($p->isZero());
        $this->assertSame(0, $p->degree());
        $this->assertSame(1, $p->leadingCoefficient());
    }

    public function testConstant(): void
    {
        $p = PolynomialImmutable::constant($this->gf256, 42);

        $this->assertSame(0, $p->degree());
        $this->assertSame(42, $p->coefficientAt(0));
    }

    public function testMonomial(): void
    {
        $p = PolynomialImmutable::monomial($this->gf256, 3, 5); // 5x³

        $this->assertSame(3, $p->degree());
        $this->assertSame(5, $p->leadingCoefficient());
        $this->assertSame(0, $p->coefficientAt(2));
        $this->assertSame(0, $p->coefficientAt(1));
        $this->assertSame(0, $p->coefficientAt(0));
    }

    public function testMonomialNegativeDegreeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PolynomialImmutable::monomial($this->gf256, -1);
    }

    public function testLeadingZerosNormalized(): void
    {
        $p = PolynomialImmutable::fromCoefficients($this->gf256, [0, 0, 5, 3]);

        $this->assertSame(1, $p->degree());
        $this->assertSame(5, $p->leadingCoefficient());
    }

    // -------------------------------------------------------------------------
    // Immutabilité
    // -------------------------------------------------------------------------

    public function testAddDoesNotMutateOriginal(): void
    {
        $p1 = PolynomialImmutable::fromCoefficients($this->gf7, [3, 2]);
        $p2 = PolynomialImmutable::fromCoefficients($this->gf7, [1, 4]);

        $sum = $p1->add($p2);

        // $p1 est inchangé
        $this->assertSame([3, 2], $p1->coefficients());
        // $sum est une nouvelle instance
        $this->assertFalse($p1->equals($sum));
    }

    public function testMultiplyDoesNotMutateOriginal(): void
    {
        $p1 = PolynomialImmutable::fromCoefficients($this->gf7, [1, 2]);
        $p2 = PolynomialImmutable::fromCoefficients($this->gf7, [1, 3]);

        $product = $p1->mul($p2);

        $this->assertSame([1, 2], $p1->coefficients());
        $this->assertSame(2, $product->degree());
    }

    // -------------------------------------------------------------------------
    // Arithmétique GF(7)
    // -------------------------------------------------------------------------

    public function testAdditionGF7(): void
    {
        // (3x + 2) + (5x + 4) = 1x + 6 dans GF(7)
        $p1  = PolynomialImmutable::fromCoefficients($this->gf7, [3, 2]);
        $p2  = PolynomialImmutable::fromCoefficients($this->gf7, [5, 4]);
        $sum = $p1->add($p2);

        $this->assertSame(1, $sum->coefficientAt(1)); // (3+5) mod 7
        $this->assertSame(6, $sum->coefficientAt(0)); // (2+4) mod 7
    }

    public function testSubtractionGF7(): void
    {
        // (3x + 2) - (5x + 4) = 5x + 5 dans GF(7)
        $p1   = PolynomialImmutable::fromCoefficients($this->gf7, [3, 2]);
        $p2   = PolynomialImmutable::fromCoefficients($this->gf7, [5, 4]);
        $diff = $p1->sub($p2);

        $this->assertSame(5, $diff->coefficientAt(1));
        $this->assertSame(5, $diff->coefficientAt(0));
    }

    public function testMultiplicationGF7(): void
    {
        // (x + 2)(x + 3) = x² + 5x + 6 dans GF(7)
        $p1      = PolynomialImmutable::fromCoefficients($this->gf7, [1, 2]);
        $p2      = PolynomialImmutable::fromCoefficients($this->gf7, [1, 3]);
        $product = $p1->mul($p2);

        $this->assertSame(2, $product->degree());
        $this->assertSame(1, $product->coefficientAt(2));
        $this->assertSame(5, $product->coefficientAt(1));
        $this->assertSame(6, $product->coefficientAt(0));
    }

    // -------------------------------------------------------------------------
    // Arithmétique GF(256)
    // -------------------------------------------------------------------------

    public function testAdditionGF256(): void
    {
        // Dans GF(2^n), addition = XOR sur les coefficients
        $p1  = PolynomialImmutable::fromCoefficients($this->gf256, [5, 3]);
        $p2  = PolynomialImmutable::fromCoefficients($this->gf256, [5, 7]);
        $sum = $p1->add($p2);

        // 5 XOR 5 = 0 → terme dominant supprimé, 3 XOR 7 = 4
        $this->assertSame(0, $sum->degree());
        $this->assertSame(4, $sum->coefficientAt(0));
    }

    public function testMultiplicationGF256(): void
    {
        // (2x + 3)(4x + 5)
        $p1      = PolynomialImmutable::fromCoefficients($this->gf256, [2, 3]);
        $p2      = PolynomialImmutable::fromCoefficients($this->gf256, [4, 5]);
        $product = $p1->mul($p2);

        $this->assertSame(2, $product->degree());
        $this->assertSame($this->gf256->multiply(2, 4), $product->coefficientAt(2));
        $this->assertSame(
            $this->gf256->add(
                $this->gf256->multiply(2, 5),
                $this->gf256->multiply(3, 4)
            ),
            $product->coefficientAt(1)
        );
        $this->assertSame($this->gf256->multiply(3, 5), $product->coefficientAt(0));
    }

    public function testScalarMultiply(): void
    {
        $p      = PolynomialImmutable::fromCoefficients($this->gf256, [2, 5]);
        $scaled = $p->scalarMul(3);

        $this->assertSame($this->gf256->multiply(3, 2), $scaled->coefficientAt(1));
        $this->assertSame($this->gf256->multiply(3, 5), $scaled->coefficientAt(0));
    }

    public function testScalarMultiplyByZero(): void
    {
        $p = PolynomialImmutable::fromCoefficients($this->gf256, [2, 5]);
        $this->assertTrue($p->scalarMul(0)->isZero());
    }

    // -------------------------------------------------------------------------
    // Division
    // -------------------------------------------------------------------------

    public function testDivmodExact(): void
    {
        // (x + 1)(x + 2) = x² + 3x + 2 dans GF(7)
        $dividend = PolynomialImmutable::fromCoefficients($this->gf7, [1, 3, 2]);
        $divisor  = PolynomialImmutable::fromCoefficients($this->gf7, [1, 1]);

        [$quotient, $remainder] = $dividend->divmod($divisor);

        $this->assertTrue($remainder->isZero());
        $this->assertTrue($quotient->mul($divisor)->equals($dividend));
    }

    public function testDivmodWithRemainder(): void
    {
        // (x² + 1) / (x + 1) dans GF(7)
        $dividend = PolynomialImmutable::fromCoefficients($this->gf7, [1, 0, 1]);
        $divisor  = PolynomialImmutable::fromCoefficients($this->gf7, [1, 1]);

        [$quotient, $remainder] = $dividend->divmod($divisor);

        // quotient * divisor + remainder = dividend
        $reconstructed = $quotient->mul($divisor)->add($remainder);
        $this->assertTrue($reconstructed->equals($dividend));
    }

    public function testDivmodByZeroThrows(): void
    {
        $p    = PolynomialImmutable::fromCoefficients($this->gf256, [1, 2]);
        $zero = PolynomialImmutable::zero($this->gf256);

        $this->expectException(\DivisionByZeroError::class);
        $p->divmod($zero);
    }

    public function testDivmodDividendSmallerThanDivisor(): void
    {
        // x / (x² + 1) → quotient = 0, remainder = x
        $dividend = PolynomialImmutable::fromCoefficients($this->gf7, [1, 0]);
        $divisor  = PolynomialImmutable::fromCoefficients($this->gf7, [1, 0, 1]);

        [$quotient, $remainder] = $dividend->divmod($divisor);

        $this->assertTrue($quotient->isZero());
        $this->assertTrue($remainder->equals($dividend));
    }

    // -------------------------------------------------------------------------
    // Évaluation
    // -------------------------------------------------------------------------

    public function testEvaluateGF7(): void
    {
        // p(x) = x² + 2x + 3, p(2) = 4 + 4 + 3 = 11 mod 7 = 4
        $p = PolynomialImmutable::fromCoefficients($this->gf7, [1, 2, 3]);
        $this->assertSame(4, $p->evaluate(2));
    }

    public function testEvaluateGF256(): void
    {
        $p        = PolynomialImmutable::fromCoefficients($this->gf256, [2, 3]);
        $expected = $this->gf256->add($this->gf256->multiply(2, 5), 3);

        $this->assertSame($expected, $p->evaluate(5));
    }

    public function testEvaluateZeroPolynomial(): void
    {
        $this->assertSame(0, PolynomialImmutable::zero($this->gf256)->evaluate(42));
    }

    public function testEvaluateAtZeroReturnsConstantTerm(): void
    {
        $p = PolynomialImmutable::fromCoefficients($this->gf256, [5, 3, 7]);
        $this->assertSame(7, $p->evaluate(0));
    }

    // -------------------------------------------------------------------------
    // Égalité
    // -------------------------------------------------------------------------

    public function testEquals(): void
    {
        $p1 = PolynomialImmutable::fromCoefficients($this->gf256, [1, 2, 3]);
        $p2 = PolynomialImmutable::fromCoefficients($this->gf256, [1, 2, 3]);
        $p3 = PolynomialImmutable::fromCoefficients($this->gf256, [1, 2, 4]);

        $this->assertTrue($p1->equals($p2));
        $this->assertFalse($p1->equals($p3));
    }

    public function testEqualsNormalized(): void
    {
        $p1 = PolynomialImmutable::fromCoefficients($this->gf256, [0, 0, 5, 3]);
        $p2 = PolynomialImmutable::fromCoefficients($this->gf256, [5, 3]);

        $this->assertTrue($p1->equals($p2));
    }

    // -------------------------------------------------------------------------
    // Cas particuliers
    // -------------------------------------------------------------------------

    public function testMultiplyByZero(): void
    {
        $p    = PolynomialImmutable::fromCoefficients($this->gf256, [1, 2, 3]);
        $zero = PolynomialImmutable::zero($this->gf256);

        $this->assertTrue($p->mul($zero)->isZero());
        $this->assertTrue($zero->mul($p)->isZero());
    }

    public function testMultiplyByOne(): void
    {
        $p   = PolynomialImmutable::fromCoefficients($this->gf256, [1, 2, 3]);
        $one = PolynomialImmutable::one($this->gf256);

        $this->assertTrue($p->mul($one)->equals($p));
        $this->assertTrue($one->mul($p)->equals($p));
    }

    public function testAddZero(): void
    {
        $p    = PolynomialImmutable::fromCoefficients($this->gf256, [1, 2, 3]);
        $zero = PolynomialImmutable::zero($this->gf256);

        $this->assertTrue($p->add($zero)->equals($p));
        $this->assertTrue($zero->add($p)->equals($p));
    }

    public function testAddToSelfGF256IsZero(): void
    {
        // Dans GF(2^n), p + p = 0
        $p = PolynomialImmutable::fromCoefficients($this->gf256, [5, 3, 7]);
        $this->assertTrue($p->add($p)->isZero());
    }

    public function testDifferentFieldsThrows(): void
    {
        $p1 = PolynomialImmutable::fromCoefficients($this->gf256, [1, 2]);
        $p2 = PolynomialImmutable::fromCoefficients($this->gf7, [1, 2]);

        $this->expectException(\InvalidArgumentException::class);
        $p1->add($p2);
    }

    public function testSameOrderDifferentInstancesWork(): void
    {
        $gf256a = new GaloisField(256);
        $gf256b = new GaloisField(256);

        $p1 = PolynomialImmutable::fromCoefficients($gf256a, [5, 3]);
        $p2 = PolynomialImmutable::fromCoefficients($gf256b, [2, 1]);
        $result = $p1->add($p2);

        $this->assertSame(7, $result->coefficientAt(1)); // 5 + 2 in GF(256)
        $this->assertSame(2, $result->coefficientAt(0)); // 3 + 1 in GF(256)
    }
}
