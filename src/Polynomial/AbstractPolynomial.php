<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Polynomial;

use Guillaumetissier\GaloisFields\Field\GaloisFieldInterface;

/**
 * Shared logic for Polynomial and PolynomialImmutable.
 */
abstract class AbstractPolynomial implements PolynomialInterface
{
    protected GaloisFieldInterface $field;

    /** @var array<int, int> [a_n, ..., a_0] */
    protected array $coefficients;

    /**
     * @param array<int, int> $coefficients
     */
    public function __construct(GaloisFieldInterface $field, array $coefficients)
    {
        $this->field = $field;
        $this->coefficients = self::normalize($coefficients);
    }

    // -------------------------------------------------------------------------
    // Read-only properties
    // -------------------------------------------------------------------------

    public function field(): GaloisFieldInterface
    {
        return $this->field;
    }

    public function degree(): int
    {
        return empty($this->coefficients) ? -1 : count($this->coefficients) - 1;
    }

    public function isZero(): bool
    {
        return empty($this->coefficients);
    }

    public function leadingCoefficient(): int
    {
        return $this->isZero() ? 0 : $this->coefficients[0];
    }

    public function coefficientAt(int $degree): int
    {
        if ($degree < 0 || $degree > $this->degree()) {
            return 0;
        }

        return $this->coefficients[$this->degree() - $degree];
    }

    /** @return array<int, int> */
    public function coefficients(): array
    {
        return $this->coefficients;
    }

    // -------------------------------------------------------------------------
    // Evaluation & equality
    // -------------------------------------------------------------------------

    public function evaluate(int $x): int
    {
        if ($this->isZero()) {
            return 0;
        }

        // Horner : ((a_n * x + a_(n-1)) * x + …) * x + a_0
        $result = 0;
        foreach ($this->coefficients as $coeff) {
            $result = $this->field->add(
                $this->field->multiply($result, $x),
                $coeff
            );
        }

        return $result;
    }

    public function equals(PolynomialInterface $other): bool
    {
        return $this->coefficients === $other->coefficients();
    }

    /** @return array<int, int> */
    protected function computeAdd(PolynomialInterface $other): array
    {
        $this->assertSameField($other);

        $maxDeg = max($this->degree(), $other->degree());
        $result = [];

        for ($deg = $maxDeg; $deg >= 0; $deg--) {
            $result[] = $this->field->add(
                $this->coefficientAt($deg),
                $other->coefficientAt($deg)
            );
        }

        return $result;
    }

    /** @return array<int, int> */
    protected function computeSubtract(PolynomialInterface $other): array
    {
        $this->assertSameField($other);

        $maxDeg = max($this->degree(), $other->degree());
        $result = [];

        for ($deg = $maxDeg; $deg >= 0; $deg--) {
            $result[] = $this->field->subtract(
                $this->coefficientAt($deg),
                $other->coefficientAt($deg)
            );
        }

        return $result;
    }

    /** @return array<int, int> */
    protected function computeMultiply(PolynomialInterface $other): array
    {
        $this->assertSameField($other);

        if ($this->isZero() || $other->isZero()) {
            return [];
        }

        $resultDegree = $this->degree() + $other->degree();
        $result = array_fill(0, $resultDegree + 1, 0);

        for ($i = 0; $i <= $this->degree(); $i++) {
            for ($j = 0; $j <= $other->degree(); $j++) {
                $product = $this->field->multiply(
                    $this->coefficientAt($this->degree() - $i),
                    $other->coefficientAt($other->degree() - $j)
                );
                $result[$i + $j] = $this->field->add($result[$i + $j], $product);
            }
        }

        return $result;
    }

    /** @return array<int, int> */
    protected function computeScalarMultiply(int $scalar): array
    {
        if ($scalar === 0) {
            return [];
        }

        $result = [];
        foreach ($this->coefficients as $coeff) {
            $result[] = $this->field->multiply($coeff, $scalar);
        }

        return $result;
    }

    /**
     * @return array{array<int, int>, array<int, int>} [quotientCoeffs, remainderCoeffs]
     */
    protected function computeDivmod(PolynomialInterface $divisor): array
    {
        $this->assertSameField($divisor);

        if ($divisor->isZero()) {
            throw new \DivisionByZeroError('Division by zero polynomial');
        }

        if ($this->degree() < $divisor->degree()) {
            return [[], $this->coefficients];
        }

        $remainderCoeffs = $this->coefficients;
        $remainderDegree = $this->degree();
        $quotientSize    = $this->degree() - $divisor->degree() + 1;
        $quotientCoeffs  = array_fill(0, $quotientSize, 0);
        $divisorCoeffs   = $divisor->coefficients();
        $divisorCount    = count($divisorCoeffs);

        while (!empty($remainderCoeffs) && $remainderDegree >= $divisor->degree()) {
            $coeffQuot  = $this->field->divide($remainderCoeffs[0], $divisor->leadingCoefficient());
            $degreeDiff = $remainderDegree - $divisor->degree();

            $quotientCoeffs[$quotientSize - 1 - $degreeDiff] = $coeffQuot;

            for ($i = 0; $i < $divisorCount; $i++) {
                $remainderCoeffs[$i] = $this->field->subtract(
                    $remainderCoeffs[$i],
                    $this->field->multiply($coeffQuot, $divisorCoeffs[$i])
                );
            }

            $remainderCoeffs  = self::normalize($remainderCoeffs);
            $remainderDegree  = empty($remainderCoeffs) ? -1 : count($remainderCoeffs) - 1;
        }

        return [$quotientCoeffs, $remainderCoeffs];
    }

    /**
     * Supprime les coefficients dominants nuls
     *
     * @param array<int, int> $coefficients
     * @return array<int, int>
     */
    protected static function normalize(array $coefficients): array
    {
        while (!empty($coefficients) && $coefficients[0] === 0) {
            array_shift($coefficients);
        }

        return $coefficients;
    }

    protected function assertSameField(PolynomialInterface $other): void
    {
        if ($this->field !== $other->field()) {
            throw new \InvalidArgumentException('Cannot operate on polynomials over different fields');
        }
    }
}
