<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Polynomial;

use Guillaumetissier\GaloisFields\Field\GaloisFieldInterface;

/**
 * Shared interface for Polynomial (mutable) and PolynomialImmutable.
 *
 * Coefficients are in descending order of degree :
 *   [a_n, a_(n-1), ..., a_1, a_0]  →  a_n*x^n + ... + a_1*x + a_0
 */
interface PolynomialInterface
{
    public function field(): GaloisFieldInterface;

    public function degree(): int;

    public function isZero(): bool;

    public function leadingCoefficient(): int;

    public function coefficientAt(int $degree): int;

    /** @return array<int, int> */
    public function coefficients(): array;

    public function evaluate(int $x): int;

    public function equals(PolynomialInterface $other): bool;

    public function add(PolynomialInterface $other): self;

    public function sub(PolynomialInterface $other): self;

    public function mul(PolynomialInterface $other): self;

    public function scalarMul(int $scalar): self;

    /** @return array{self, self} */
    public function divmod(PolynomialInterface $divisor): array;

    public function div(PolynomialInterface $divisor): self;

    public function mod(PolynomialInterface $divisor): self;
}
