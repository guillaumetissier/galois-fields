<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Polynomial;

use Guillaumetissier\GaloisFields\Field\GaloisFieldInterface;

/**
 * Mutable polynomial over a Galois field.
 * Arithmetic operations modify $this in place and return $this (fluent).
 */
final class Polynomial extends AbstractPolynomial
{
    /**
     * @param array<int, int> $coefficients Descending order [a_n, ..., a_0]
     */
    public static function fromCoefficients(GaloisFieldInterface $field, array $coefficients): self
    {
        return new self($field, $coefficients);
    }

    public static function zero(GaloisFieldInterface $field): self
    {
        return new self($field, []);
    }

    public static function one(GaloisFieldInterface $field): self
    {
        return new self($field, [1]);
    }

    public static function constant(GaloisFieldInterface $field, int $value): self
    {
        return new self($field, [$value]);
    }

    /** coefficient * x^degree */
    public static function monomial(GaloisFieldInterface $field, int $degree, int $coefficient = 1): self
    {
        if ($degree < 0) {
            throw new \InvalidArgumentException('Degree must be >= 0');
        }

        $coefficients = array_fill(0, $degree + 1, 0);
        $coefficients[0] = $coefficient;

        return new self($field, $coefficients);
    }

    public function add(PolynomialInterface $other): self
    {
        $this->coefficients = $this->computeAdd($other);

        return $this;
    }

    public function sub(PolynomialInterface $other): self
    {
        $this->coefficients = $this->computeSubtract($other);

        return $this;
    }

    public function mul(PolynomialInterface $other): self
    {
        $this->coefficients = $this->computeMultiply($other);

        return $this;
    }

    public function scalarMul(int $scalar): self
    {
        $this->coefficients = $this->computeScalarMultiply($scalar);

        return $this;
    }

    /**
     * @return array{self, self}
     */
    public function divmod(PolynomialInterface $divisor): array
    {
        [$qCoeffs, $rCoeffs] = $this->computeDivmod($divisor);

        $this->coefficients = $rCoeffs;

        return [
            new self($this->field, $qCoeffs),
            $this,
        ];
    }

    public function div(PolynomialInterface $divisor): self
    {
        [$qCoeffs] = $this->computeDivmod($divisor);
        $this->coefficients = $qCoeffs;

        return $this;
    }

    public function mod(PolynomialInterface $divisor): self
    {
        [, $rCoeffs] = $this->computeDivmod($divisor);
        $this->coefficients = $rCoeffs;

        return $this;
    }

    /**
     * Replace all coefficients
     *
     * @param array<int, int> $coefficients Descending order [a_n, ..., a_0]
     */
    public function setCoefficients(array $coefficients): self
    {
        $this->coefficients = self::normalize($coefficients);

        return $this;
    }

    /**
     * modify coefficient of x^$degree
     */
    public function setCoefficientAt(int $degree, int $value): self
    {
        if ($degree < 0) {
            throw new \InvalidArgumentException('Degree must be >= 0');
        }

        // extends the array if degree greater than Polynomial's degree
        if ($degree > $this->degree()) {
            $newCoeffs = array_fill(0, $degree + 1, 0);
            $offset = $degree - max($this->degree(), 0);
            foreach ($this->coefficients as $i => $coeff) {
                $newCoeffs[$i + $offset] = $coeff;
            }
            $this->coefficients = $newCoeffs;
        }

        $this->coefficients[$this->degree() - $degree] = $value;
        $this->coefficients = self::normalize($this->coefficients);

        return $this;
    }
}
