<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Tests\Polynomial;

use Guillaumetissier\GaloisFields\GaloisField;
use Guillaumetissier\GaloisFields\Polynomial\Polynomial;
use Guillaumetissier\GaloisFields\Polynomial\PolynomialArithmetic;
use Guillaumetissier\GaloisFields\Polynomial\PolynomialFormatter;
use Guillaumetissier\GaloisFields\Polynomial\PolynomialImmutable;
use PHPUnit\Framework\TestCase;

class PolynomialArithmeticTest extends TestCase
{
    private GaloisField $gf256;
    private GaloisField $gf7;
    private PolynomialArithmetic $arithGf256;
    private PolynomialArithmetic $arithGf7;

    protected function setUp(): void
    {
        $this->gf256      = new GaloisField(256);
        $this->gf7        = new GaloisField(7);
        $this->arithGf256 = new PolynomialArithmetic($this->gf256);
        $this->arithGf7   = new PolynomialArithmetic($this->gf7);
    }

    // -------------------------------------------------------------------------
    // GCD
    // -------------------------------------------------------------------------

    public function testGcdCoprime(): void
    {
        // (x + 1) et (x + 2) sont premiers entre eux dans GF(7)
        $p1  = PolynomialImmutable::fromCoefficients($this->gf7, [1, 1]);
        $p2  = PolynomialImmutable::fromCoefficients($this->gf7, [1, 2]);
        $gcd = $this->arithGf7->gcd($p1, $p2);

        $this->assertSame(0, $gcd->degree());
        $this->assertSame(1, $gcd->leadingCoefficient()); // monic
    }

    public function testGcdWithCommonFactor(): void
    {
        // p1 = (x+1)(x+2) = x² + 3x + 2
        // p2 = (x+1)(x+3) = x² + 4x + 3
        // GCD = x + 1
        $p1  = PolynomialImmutable::fromCoefficients($this->gf7, [1, 3, 2]);
        $p2  = PolynomialImmutable::fromCoefficients($this->gf7, [1, 4, 3]);
        $gcd = $this->arithGf7->gcd($p1, $p2);

        $this->assertSame(1, $gcd->degree());
        $this->assertSame(1, $gcd->leadingCoefficient());

        // Le GCD divise les deux
        $this->assertTrue($p1->mod($gcd)->isZero());
        $this->assertTrue($p2->mod($gcd)->isZero());
    }

    public function testGcdWithSelf(): void
    {
        $p   = PolynomialImmutable::fromCoefficients($this->gf7, [1, 2, 3]);
        $gcd = $this->arithGf7->gcd($p, $p);

        $this->assertTrue($gcd->equals($p));
    }

    public function testGcdWithZero(): void
    {
        // GCD(p, 0) = p (monic)
        $p    = PolynomialImmutable::fromCoefficients($this->gf7, [1, 2]);
        $zero = PolynomialImmutable::zero($this->gf7);
        $gcd  = $this->arithGf7->gcd($p, $zero);

        $this->assertTrue($gcd->equals($p));
    }

    public function testGcdDoesNotMutateInputs(): void
    {
        // On passe des Polynomial mutables — gcd ne doit pas les modifier
        $p1 = Polynomial::fromCoefficients($this->gf7, [1, 3, 2]);
        $p2 = Polynomial::fromCoefficients($this->gf7, [1, 4, 3]);

        $this->arithGf7->gcd($p1, $p2);

        // Les originaux sont inchangés
        $this->assertSame([1, 3, 2], $p1->coefficients());
        $this->assertSame([1, 4, 3], $p2->coefficients());
    }

    public function testAreCoprime(): void
    {
        $p1 = PolynomialImmutable::fromCoefficients($this->gf7, [1, 1]);
        $p2 = PolynomialImmutable::fromCoefficients($this->gf7, [1, 2]);

        $this->assertTrue($this->arithGf7->areCoprime($p1, $p2));
    }

    public function testAreNotCoprime(): void
    {
        $p1 = PolynomialImmutable::fromCoefficients($this->gf7, [1, 3, 2]);
        $p2 = PolynomialImmutable::fromCoefficients($this->gf7, [1, 4, 3]);

        $this->assertFalse($this->arithGf7->areCoprime($p1, $p2));
    }

    public function testMultiEvaluate(): void
    {
        // p(x) = x² + 1 dans GF(7)
        $p = PolynomialImmutable::fromCoefficients($this->gf7, [1, 0, 1]);
        $points = [0, 1, 2, 3];
        $results = $this->arithGf7->multiEvaluate($p, $points);

        // p(0)=1, p(1)=2, p(2)=5, p(3)=10 mod 7=3
        $this->assertSame(1, $results[0]);
        $this->assertSame(2, $results[1]);
        $this->assertSame(5, $results[2]);
        $this->assertSame(3, $results[3]);
    }

    public function testMultiEvaluateGF256(): void
    {
        $p = PolynomialImmutable::fromCoefficients($this->gf256, [2, 3]); // 2x + 3
        $points = [0, 1, 2, 5];
        $results = $this->arithGf256->multiEvaluate($p, $points);

        foreach ($points as $i => $point) {
            $expected = $this->gf256->add($this->gf256->multiply(2, $point), 3);
            $this->assertSame($expected, $results[$i], "Échec au point $point");
        }
    }

    public function testInterpolateLinear(): void
    {
        // Dans GF(7) : trouver p passant par (0,3) et (1,5)
        $p = $this->arithGf7->interpolate([0, 1], [3, 5]);

        $this->assertSame(3, $p->evaluate(0));
        $this->assertSame(5, $p->evaluate(1));
    }

    public function testInterpolateQuadratic(): void
    {
        $xs = [0, 1, 2];
        $ys = [1, 3, 2];
        $p = $this->arithGf7->interpolate($xs, $ys);

        $this->assertSame(1, $p->evaluate(0));
        $this->assertSame(3, $p->evaluate(1));
        $this->assertSame(2, $p->evaluate(2));
    }

    public function testInterpolateRoundTripGF256(): void
    {
        $original = PolynomialImmutable::fromCoefficients($this->gf256, [1, 5, 3, 7]);
        $xs = [1, 2, 3, 4]; // degré + 1 points
        $ys = [];
        foreach ($xs as $x) {
            $ys[] = $original->evaluate($x);
        }
        $interpolated = $this->arithGf256->interpolate($xs, $ys);

        $this->assertTrue($original->equals($interpolated));
    }

    public function testInterpolateRetourneImmutable(): void
    {
        $p = $this->arithGf7->interpolate([0, 1], [3, 5]);

        $this->assertInstanceOf(PolynomialImmutable::class, $p);
    }

    public function testInterpolateEmptyRetourneZero(): void
    {
        $p = $this->arithGf7->interpolate([], []);

        $this->assertTrue($p->isZero());
    }

    public function testInterpolateLongueursDifferentesThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->arithGf7->interpolate([0, 1], [3]);
    }

    public function testInterpolateDoublonsThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->arithGf7->interpolate([0, 0, 1], [3, 5, 2]);
    }

    public function testDerivativeLinear(): void
    {
        // d/dx(3x + 2) = 3 dans GF(7)
        $p = PolynomialImmutable::fromCoefficients($this->gf7, [3, 2]);
        $d = $this->arithGf7->derivative($p);

        $this->assertSame(0, $d->degree());
        $this->assertSame(3, $d->coefficientAt(0));
    }

    public function testDerivativeQuadratic(): void
    {
        // d/dx(x² + 3x + 5) = 2x + 3 dans GF(7)
        $p = PolynomialImmutable::fromCoefficients($this->gf7, [1, 3, 5]);
        $d = $this->arithGf7->derivative($p);

        $this->assertSame(1, $d->degree());
        $this->assertSame(2, $d->coefficientAt(1));
        $this->assertSame(3, $d->coefficientAt(0));
    }

    public function testDerivativeConstant(): void
    {
        $p = PolynomialImmutable::constant($this->gf7, 5);

        $this->assertTrue($this->arithGf7->derivative($p)->isZero());
    }

    public function testDerivativeInCharacteristic2(): void
    {
        // Dans GF(2^n) : d/dx(x² + x + 1) = 0 + 1 = 1
        // (le terme x² s'annule car 2 mod 2 = 0)
        $p = PolynomialImmutable::fromCoefficients($this->gf256, [1, 1, 1]);
        $d = $this->arithGf256->derivative($p);

        $this->assertSame(0, $d->degree());
        $this->assertSame(1, $d->coefficientAt(0));
    }

    public function testDerivativeZero(): void
    {
        $zero = PolynomialImmutable::zero($this->gf7);
        $this->assertTrue($this->arithGf7->derivative($zero)->isZero());
    }

    public function testDerivativeReturnsImmutable(): void
    {
        $p = PolynomialImmutable::fromCoefficients($this->gf7, [1, 3, 5]);
        $d = $this->arithGf7->derivative($p);

        $this->assertInstanceOf(PolynomialImmutable::class, $d);
    }
}
