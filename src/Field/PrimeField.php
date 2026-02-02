<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Field;

/**
 * Implementation of prime Galois fields GF(p) where p is prime.
 * Uses simple modular arithmetic.
 */
final class PrimeField implements GaloisFieldInterface
{
    private int $prime;

    public function __construct(int $prime)
    {
        $this->prime = $prime;
    }

    public function getOrder(): int
    {
        return $this->prime;
    }

    public function getCharacteristic(): int
    {
        return $this->prime;
    }

    public function getDegree(): int
    {
        return 1;
    }

    public function add(int $a, int $b): int
    {
        return ($a + $b) % $this->prime;
    }

    public function subtract(int $a, int $b): int
    {
        return ($a - $b + $this->prime) % $this->prime;
    }

    public function multiply(int $a, int $b): int
    {
        return ($a * $b) % $this->prime;
    }

    public function divide(int $a, int $b): int
    {
        if ($b === 0) {
            throw new \DivisionByZeroError('Division by zero in Galois field');
        }

        return $this->multiply($a, $this->inverse($b));
    }

    public function inverse(int $element): int
    {
        if ($element === 0) {
            throw new \DivisionByZeroError('Zero has no multiplicative inverse');
        }

        // Use extended Euclidean algorithm
        return $this->modInverse($element, $this->prime);
    }

    public function power(int $element, int $exponent): int
    {
        if ($exponent < 0) {
            $element = $this->inverse($element);
            $exponent = -$exponent;
        }

        $result = 1;
        $base = $element % $this->prime;

        while ($exponent > 0) {
            if ($exponent % 2 === 1) {
                $result = ($result * $base) % $this->prime;
            }
            $base = ($base * $base) % $this->prime;
            $exponent >>= 1;
        }

        return $result;
    }

    public function isValidElement(int $element): bool
    {
        return $element >= 0 && $element < $this->prime;
    }

    /**
     * Extended Euclidean algorithm to find modular inverse
     */
    private function modInverse(int $a, int $m): int
    {
        $a = $a % $m;

        $m0 = $m;
        $x0 = 0;
        $x1 = 1;

        if ($m === 1) {
            return 0;
        }

        while ($a > 1) {
            $q = intdiv($a, $m);
            $t = $m;

            $m = $a % $m;
            $a = $t;
            $t = $x0;

            $x0 = $x1 - $q * $x0;
            $x1 = $t;
        }

        if ($x1 < 0) {
            $x1 += $m0;
        }

        return $x1;
    }
}
