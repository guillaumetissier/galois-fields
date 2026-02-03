<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Polynomial;

use Guillaumetissier\GaloisFields\Field\GaloisFieldInterface;

/**
 * Higher-level polynomial operations over a Galois field.
 *
 * Always returns PolynomialImmutable
 */
final class PolynomialArithmetic
{
    private GaloisFieldInterface $field;

    public function __construct(GaloisFieldInterface $field)
    {
        $this->field = $field;
    }

    /**
     * Greater common divisor
     */
    public function gcd(PolynomialInterface $a, PolynomialInterface $b): PolynomialImmutable
    {
        $a = PolynomialImmutable::fromCoefficients($this->field, $a->coefficients());
        $b = PolynomialImmutable::fromCoefficients($this->field, $b->coefficients());

        while (!$b->isZero()) {
            $remainder = $a->mod($b);
            $a = $b;
            $b = $remainder;
        }

        if (!$a->isZero() && $a->leadingCoefficient() !== 1) {
            $a = $a->scalarMul(
                $this->field->inverse($a->leadingCoefficient())
            );
        }

        return $a;
    }

    public function areCoprime(PolynomialInterface $a, PolynomialInterface $b): bool
    {
        return $this->gcd($a, $b)->degree() === 0;
    }

    /**
     * Evaluation at several points
     *
     * @param array<int, int> $points
     * @return array<int, int>
     */
    public function multiEvaluate(PolynomialInterface $polynomial, array $points): array
    {
        $results = [];
        foreach ($points as $i => $point) {
            $results[$i] = $polynomial->evaluate($point);
        }
        return $results;
    }

    /**
     * Lagrange Interpolation
     *
     * @param array<int, int> $xs  Abscises (distinct values)
     * @param array<int, int> $ys  Ordinates (same length as $xs)
     * @return PolynomialImmutable interpolated polynomial
     * @throws \InvalidArgumentException if polynomials have different lengths or $xs has doubles
     */
    public function interpolate(array $xs, array $ys): PolynomialImmutable
    {
        if (count($xs) !== count($ys)) {
            throw new \InvalidArgumentException('$xs and $ys must have the same length');
        }

        $n = count($xs);

        if ($n === 0) {
            return PolynomialImmutable::zero($this->field);
        }

        if (count(array_unique($xs)) !== $n) {
            throw new \InvalidArgumentException('$xs must contain unique values');
        }

        $result = PolynomialImmutable::zero($this->field);

        for ($i = 0; $i < $n; $i++) {
            if ($ys[$i] === 0) {
                continue;
            }

            $basis = PolynomialImmutable::one($this->field);
            $denominator = 1;

            for ($j = 0; $j < $n; $j++) {
                if ($i === $j) {
                    continue;
                }

                // basis *= (x - x_j)
                $factor = PolynomialImmutable::fromCoefficients($this->field, [
                    1,
                    $this->field->subtract(0, $xs[$j])
                ]);
                $basis = $basis->mul($factor);

                // denominator *= (x_i - x_j)
                $denominator = $this->field->multiply(
                    $denominator,
                    $this->field->subtract($xs[$i], $xs[$j])
                );
            }

            // L_i(x) = basis * (y_i / denominator)
            $scale  = $this->field->multiply($ys[$i], $this->field->inverse($denominator));
            $basis  = $basis->scalarMul($scale);
            $result = $result->add($basis);
        }

        return $result;
    }

    /**
     * Dérivée formelle d'un polynôme.
     * Dans GF(p), la dérivée de x^n est (n mod p) * x^(n-1).
     *
     * En particulier dans GF(2^n) : tous les termes de degré pair s'annulent.
     */
    public function derivative(PolynomialInterface $polynomial): PolynomialImmutable
    {
        if ($polynomial->degree() <= 0) {
            return PolynomialImmutable::zero($this->field);
        }

        $coefficients = [];
        $degree       = $polynomial->degree();
        $p            = $this->field->getCharacteristic();

        for ($i = $degree; $i >= 1; $i--) {
            $coeff = $polynomial->coefficientAt($i);

            // Multiplier par i mod p (via des additions répétées dans le champ)
            $n            = $i % $p;
            $derivedCoeff = 0;
            for ($k = 0; $k < $n; $k++) {
                $derivedCoeff = $this->field->add($derivedCoeff, $coeff);
            }

            $coefficients[] = $derivedCoeff;
        }

        return PolynomialImmutable::fromCoefficients($this->field, $coefficients);
    }
}
