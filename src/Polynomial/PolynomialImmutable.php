<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Polynomial;

use Guillaumetissier\GaloisFields\Field\GaloisFieldInterface;

/**
 * Immutable polynomial over a Galois field.
 * Every arithmetic operation returns a new instance.
 */
final class PolynomialImmutable extends AbstractPolynomial
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
        return new self($this->field, $this->computeAdd($other));
    }

    public function sub(PolynomialInterface $other): self
    {
        return new self($this->field, $this->computeSubtract($other));
    }

    public function mul(PolynomialInterface $other): self
    {
        return new self($this->field, $this->computeMultiply($other));
    }

    public function scalarMul(int $scalar): self
    {
        return new self($this->field, $this->computeScalarMultiply($scalar));
    }

    /** @return array{self, self} */
    public function divmod(PolynomialInterface $divisor): array
    {
        [$qCoeffs, $rCoeffs] = $this->computeDivmod($divisor);

        return [
            new self($this->field, $qCoeffs),
            new self($this->field, $rCoeffs),
        ];
    }

    public function div(PolynomialInterface $divisor): self
    {
        return $this->divmod($divisor)[0];
    }

    public function mod(PolynomialInterface $divisor): self
    {
        return $this->divmod($divisor)[1];
    }
}
