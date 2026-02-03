<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Field;

/**
 * Interface for Galois Field operations
 */
interface GaloisFieldInterface
{
    /**
     * Get the order (size) of the field
     */
    public function getOrder(): int;

    /**
     * Get the characteristic (prime p) of the field
     */
    public function getCharacteristic(): int;

    /**
     * Get the degree (exponent n) where order = characteristic^degree
     */
    public function getDegree(): int;

    public function isBinary(): bool;

    /**
     * Convert an element to its alpha power representation (e.g., 4 → "α^2").
     * Only meaningful for binary extension fields GF(2^n).
     *
     * @throws \BadMethodCallException if not a binary extension field
     */
    public function toAlphaPower(int $element): string;

    /**
     * Add two elements in the field
     */
    public function add(int $a, int $b): int;

    /**
     * Subtract two elements in the field
     */
    public function subtract(int $a, int $b): int;

    /**
     * Multiply two elements in the field
     */
    public function multiply(int $a, int $b): int;

    /**
     * Divide two elements in the field
     *
     * @throws \DivisionByZeroError if $b is 0
     */
    public function divide(int $a, int $b): int;

    /**
     * Compute the multiplicative inverse of an element
     *
     * @throws \DivisionByZeroError if $element is 0
     */
    public function inverse(int $element): int;

    /**
     * Raise an element to a power
     */
    public function power(int $element, int $exponent): int;

    /**
     * Check if a value is a valid element of this field
     */
    public function isValidElement(int $element): bool;
}
