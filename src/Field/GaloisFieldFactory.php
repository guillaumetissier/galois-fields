<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Field;

use Guillaumetissier\GaloisFields\Exception\InvalidFieldOrderException;

/**
 * Factory for creating Galois field instances
 */
final class GaloisFieldFactory
{
    /**
     * Create a Galois field GF(order)
     *
     * @throws InvalidFieldOrderException if order is not a valid prime power
     */
    public static function create(int $order): GaloisFieldInterface
    {
        if ($order < 2) {
            throw InvalidFieldOrderException::tooSmall($order);
        }

        [$prime, $exponent] = self::factorize($order);

        if ($exponent === 0) {
            throw InvalidFieldOrderException::notPrimePower($order);
        }

        // For prime fields GF(p)
        if ($exponent === 1) {
            return new PrimeField($prime);
        }

        // For binary extension fields GF(2^n)
        if ($prime === 2) {
            if (!PrimitivePolynomials::has($prime, $exponent)) {
                throw new \RuntimeException(
                    sprintf('GF(2^%d) is not currently supported. Maximum supported degree is 16.', $exponent)
                );
            }
            return new BinaryExtensionField($exponent);
        }

        // For other extension fields GF(p^n) where p > 2
        throw new \RuntimeException(
            sprintf('GF(%d^%d) is not currently supported.', $prime, $exponent)
        );
    }

    /**
     * Factorize order into [prime, exponent] such that order = prime^exponent
     * Returns [0, 0] if order is not a prime power
     *
     * @return array{int, int}
     */
    private static function factorize(int $order): array
    {
        // Try each potential prime factor
        for ($prime = 2; $prime * $prime <= $order; $prime++) {
            if ($order % $prime !== 0) {
                continue;
            }

            $exponent = 0;
            $temp = $order;

            // Count how many times this prime divides the order
            while ($temp % $prime === 0) {
                $temp /= $prime;
                $exponent++;
            }

            // If there's a remainder, order is not a prime power
            if ($temp !== 1) {
                return [0, 0];
            }

            return [$prime, $exponent];
        }

        // If we get here, order is prime
        return [$order, 1];
    }

    /**
     * Check if a given order is valid (i.e., is a prime power)
     */
    public static function isValidOrder(int $order): bool
    {
        if ($order < 2) {
            return false;
        }

        [$prime, $exponent] = self::factorize($order);
        return $exponent > 0;
    }

    /**
     * Get the prime and exponent for a given order
     *
     * @return array{int, int}|null Returns [prime, exponent] or null if invalid
     */
    public static function getPrimeAndExponent(int $order): ?array
    {
        if ($order < 2) {
            return null;
        }

        [$prime, $exponent] = self::factorize($order);

        return $exponent > 0 ? [$prime, $exponent] : null;
    }
}
