<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Tests\Polynomial;

use Guillaumetissier\GaloisFields\GaloisField;
use Guillaumetissier\GaloisFields\Polynomial\Polynomial;
use PHPUnit\Framework\TestCase;

final class PolynomialTest extends TestCase
{
    private GaloisField $gf256;
    private GaloisField $gf7;

    protected function setUp(): void
    {
        $this->gf256 = new GaloisField(256);
        $this->gf7   = new GaloisField(7);
    }

    public function testAddMutatesInPlace(): void
    {
        $p1 = Polynomial::fromCoefficients($this->gf7, [3, 2]);
        $p2 = Polynomial::fromCoefficients($this->gf7, [5, 4]);
        $result = $p1->add($p2);

        $this->assertSame($p1, $result);
        $this->assertSame(1, $p1->coefficientAt(1));
        $this->assertSame(6, $p1->coefficientAt(0));
    }

    public function testSubtractMutatesInPlace(): void
    {
        $p1 = Polynomial::fromCoefficients($this->gf7, [3, 2]);
        $p2 = Polynomial::fromCoefficients($this->gf7, [5, 4]);
        $result = $p1->sub($p2);

        $this->assertSame($p1, $result);
        $this->assertSame(5, $p1->coefficientAt(1));
        $this->assertSame(5, $p1->coefficientAt(0));
    }

    public function testMultiplyMutatesInPlace(): void
    {
        $p1 = Polynomial::fromCoefficients($this->gf7, [1, 2]);
        $p2 = Polynomial::fromCoefficients($this->gf7, [1, 3]);
        $result = $p1->mul($p2);

        $this->assertSame($p1, $result);
        $this->assertSame(2, $p1->degree());
    }

    public function testScalarMultiplyMutatesInPlace(): void
    {
        $p = Polynomial::fromCoefficients($this->gf256, [2, 5]);
        $result = $p->scalarMul(3);

        $this->assertSame($p, $result);
        $this->assertSame($this->gf256->multiply(3, 2), $p->coefficientAt(1));
        $this->assertSame($this->gf256->multiply(3, 5), $p->coefficientAt(0));
    }

    public function testModuleMutatesInPlace(): void
    {
        // x² + 3x + 2 mod (x + 1) = 0 in GF(7)
        $p = Polynomial::fromCoefficients($this->gf7, [1, 3, 2]);
        $divisor = Polynomial::fromCoefficients($this->gf7, [1, 1]);

        $result = $p->mod($divisor);

        $this->assertSame($p, $result);
        $this->assertTrue($p->isZero());
    }

    public function testDivideMutatesInPlace(): void
    {
        // (x² + 3x + 2) / (x + 1) = (x + 2) dans GF(7)
        $p = Polynomial::fromCoefficients($this->gf7, [1, 3, 2]);
        $divisor = Polynomial::fromCoefficients($this->gf7, [1, 1]);

        $result = $p->div($divisor);

        $this->assertSame($p, $result);
        $this->assertSame(1, $p->degree());
    }

    // -------------------------------------------------------------------------
    // Fluent chaining
    // -------------------------------------------------------------------------

    public function testFluentChaining(): void
    {
        // build (x + 1) * (x + 2) * 3 in GF(7)
        $p = Polynomial::fromCoefficients($this->gf7, [1, 1])  // x + 1
        ->mul(Polynomial::fromCoefficients($this->gf7, [1, 2]))  // * (x + 2)
        ->scalarMul(3);

        // (x + 1)(x + 2) = x² + 3x + 2, puis * 3 = 3x² + 2x + 6
        $this->assertSame(2, $p->degree());
        $this->assertSame(3, $p->coefficientAt(2));
        $this->assertSame(2, $p->coefficientAt(1)); // 3*3 mod 7 = 2
        $this->assertSame(6, $p->coefficientAt(0)); // 3*2 mod 7 = 6
    }

    public function testSetCoefficients(): void
    {
        $p = Polynomial::zero($this->gf256);
        $p->setCoefficients([1, 2, 3]);

        $this->assertSame(2, $p->degree());
        $this->assertSame([1, 2, 3], $p->coefficients());
    }

    public function testSetCoefficientAt(): void
    {
        $p = Polynomial::fromCoefficients($this->gf256, [1, 2, 3]); // x² + 2x + 3
        $p->setCoefficientAt(1, 5); // x² + 5x + 3

        $this->assertSame(5, $p->coefficientAt(1));
        $this->assertSame(1, $p->coefficientAt(2));
        $this->assertSame(3, $p->coefficientAt(0));
    }

    public function testSetCoefficientAtExtendsPolynomial(): void
    {
        $p = Polynomial::fromCoefficients($this->gf256, [1, 2]); // x + 2
        $p->setCoefficientAt(3, 5); // 5x³ + x + 2

        $this->assertSame(3, $p->degree());
        $this->assertSame(5, $p->coefficientAt(3));
        $this->assertSame(0, $p->coefficientAt(2));
        $this->assertSame(1, $p->coefficientAt(1));
        $this->assertSame(2, $p->coefficientAt(0));
    }

    public function testSetCoefficientAtLeadingZeroNormalizes(): void
    {
        $p = Polynomial::fromCoefficients($this->gf256, [1, 2, 3]); // x² + 2x + 3
        $p->setCoefficientAt(2, 0); // 0x² + 2x + 3 → 2x + 3

        $this->assertSame(1, $p->degree());
        $this->assertSame(2, $p->leadingCoefficient());
    }

    public function testSetCoefficientAtNegativeDegreeThrows(): void
    {
        $p = Polynomial::fromCoefficients($this->gf256, [1, 2]);

        $this->expectException(\InvalidArgumentException::class);
        $p->setCoefficientAt(-1, 5);
    }

    public function testDivmodRemainderIsSelf(): void
    {
        $p       = Polynomial::fromCoefficients($this->gf7, [1, 0, 1]); // x² + 1
        $divisor = Polynomial::fromCoefficients($this->gf7, [1, 1]);    // x + 1
        [$quotient, $remainder] = $p->divmod($divisor);

        $this->assertSame($p, $remainder);
        $this->assertNotSame($p, $quotient);

        $reconstructed = $quotient->mul($divisor)->add($remainder);
        $this->assertSame(1, $reconstructed->coefficientAt(2));
        $this->assertSame(0, $reconstructed->coefficientAt(1));
        $this->assertSame(1, $reconstructed->coefficientAt(0));
    }
}
