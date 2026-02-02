<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Field;

/**
 * Implementation of binary extension fields GF(2^n).
 * Uses logarithm and exponential tables for efficient multiplication.
 */
final class BinaryExtensionField implements GaloisFieldInterface
{
    private int $degree;
    private int $order;
    private int $primitivePolynomial;

    /** @var array<int, int> Exponential table: exp[i] = α^i */
    private array $exp = [];

    /** @var array<int, int> Logarithm table: log[α^i] = i */
    private array $log = [];

    public function __construct(int $degree)
    {
        $this->degree = $degree;
        $this->order = 1 << $degree; // 2^degree
        $polynomial = PrimitivePolynomials::get(2, $degree);

        // For binary fields, the polynomial is always an int
        if (!is_int($polynomial)) {
            throw new \RuntimeException('Binary extension fields require integer primitive polynomials');
        }

        $this->primitivePolynomial = $polynomial;

        $this->buildTables();
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getCharacteristic(): int
    {
        return 2;
    }

    public function getDegree(): int
    {
        return $this->degree;
    }

    public function add(int $a, int $b): int
    {
        // Addition in GF(2^n) is XOR
        return $a ^ $b;
    }

    public function subtract(int $a, int $b): int
    {
        // Subtraction is the same as addition in characteristic 2
        return $a ^ $b;
    }

    public function multiply(int $a, int $b): int
    {
        if ($a === 0 || $b === 0) {
            return 0;
        }

        // Multiply using logarithm tables: α^a * α^b = α^(a+b)
        $logSum = $this->log[$a] + $this->log[$b];

        // Reduce modulo (order - 1) since α^(order-1) = 1
        $logSum %= ($this->order - 1);

        return $this->exp[$logSum];
    }

    public function divide(int $a, int $b): int
    {
        if ($b === 0) {
            throw new \DivisionByZeroError('Division by zero in Galois field');
        }

        if ($a === 0) {
            return 0;
        }

        // Divide using logarithm tables: α^a / α^b = α^(a-b)
        $logDiff = $this->log[$a] - $this->log[$b];

        // Handle negative results
        if ($logDiff < 0) {
            $logDiff += ($this->order - 1);
        }

        return $this->exp[$logDiff];
    }

    public function inverse(int $element): int
    {
        if ($element === 0) {
            throw new \DivisionByZeroError('Zero has no multiplicative inverse');
        }

        // Inverse of α^i is α^(-i) = α^(order-1-i)
        $logInverse = ($this->order - 1) - $this->log[$element];

        return $this->exp[$logInverse];
    }

    public function power(int $element, int $exponent): int
    {
        if ($element === 0) {
            return $exponent === 0 ? 1 : 0;
        }

        if ($exponent === 0) {
            return 1;
        }

        if ($exponent < 0) {
            $element = $this->inverse($element);
            $exponent = -$exponent;
        }

        // Power using logarithm tables: (α^a)^b = α^(a*b)
        $logPower = ($this->log[$element] * $exponent) % ($this->order - 1);

        return $this->exp[$logPower];
    }

    public function isValidElement(int $element): bool
    {
        return $element >= 0 && $element < $this->order;
    }

    /**
     * Build exponential and logarithm tables for the field
     */
    private function buildTables(): void
    {
        $exp = 1;

        // Build tables for all non-zero elements (order - 1 elements)
        for ($rank = 0; $rank < $this->order - 1; $rank++) {
            $this->exp[$rank] = $exp;
            $this->log[$exp] = $rank;

            // Multiply by α (shift left by 1)
            $exp <<= 1;

            // If we overflow (bit at position degree is set), reduce by primitive polynomial
            if ($exp & $this->order) {
                $exp ^= $this->primitivePolynomial;
            }
        }

        // The multiplicative group is cyclic of order (order-1)
        // So α^(order-1) = 1, which means exp[order-1] should equal exp[0]
        // We add this for convenience in the inverse calculation
        $this->exp[$this->order - 1] = $this->exp[0];

        // Note: log[0] is undefined (0 is not in the multiplicative group)
        // Some implementations set it to -1 or a sentinel value
    }

    /**
     * Get the primitive polynomial used for this field
     */
    public function getPrimitivePolynomial(): int
    {
        return $this->primitivePolynomial;
    }

    /**
     * Get the exponential table (for debugging/testing)
     *
     * @return array<int, int>
     */
    public function getExpTable(): array
    {
        return $this->exp;
    }

    /**
     * Get the logarithm table (for debugging/testing)
     *
     * @return array<int, int>
     */
    public function getLogTable(): array
    {
        return $this->log;
    }
}
