<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Field;

use RuntimeException;

/**
 * Storage for primitive polynomials used to construct Galois fields GF(p^n).
 *
 * For GF(2^n), polynomials are stored as integers where bit positions represent coefficients.
 * For example, x^8 + x^4 + x^3 + x^2 + 1 = 0x11D (binary: 100011101)
 */
final class PrimitivePolynomials
{
    /**
     * Primitive polynomials for GF(2^n)
     * Index n contains the primitive polynomial for GF(2^n)
     */
    private const GF2_POLYNOMIALS = [
        2 => 0x7,      // x^2 + x + 1
        3 => 0xB,      // x^3 + x + 1
        4 => 0x13,     // x^4 + x + 1
        5 => 0x25,     // x^5 + x^2 + 1
        6 => 0x43,     // x^6 + x + 1
        7 => 0x89,     // x^7 + x^3 + 1
        8 => 0x11D,    // x^8 + x^4 + x^3 + x^2 + 1 (used in QR codes, AES)
        9 => 0x211,    // x^9 + x^4 + 1
        10 => 0x409,   // x^10 + x^3 + 1
        11 => 0x805,   // x^11 + x^2 + 1
        12 => 0x1053,  // x^12 + x^6 + x^4 + x + 1
        13 => 0x201B,  // x^13 + x^4 + x^3 + x + 1
        14 => 0x4443,  // x^14 + x^10 + x^6 + x + 1
        15 => 0x8003,  // x^15 + x + 1
        16 => 0x1002B, // x^16 + x^5 + x^3 + x + 1
    ];

    /**
     * Primitive polynomials for GF(3^n) represented as coefficient arrays
     * [a_n, ..., a_1, a_0] represents a_n*x^n + ... + a_1*x + a_0
     */
    private const GF3_POLYNOMIALS = [
        2 => [1, 0, 2],    // x^2 + 2
        3 => [1, 2, 0, 1], // x^3 + 2x + 1
        4 => [1, 0, 0, 2, 2], // x^4 + 2x + 2
        5 => [1, 0, 2, 0, 0, 1], // x^5 + 2x^2 + 1
    ];

    /**
     * Primitive polynomials for GF(5^n)
     */
    private const GF5_POLYNOMIALS = [
        2 => [1, 0, 2],    // x^2 + 2
        3 => [1, 0, 1, 2], // x^3 + x + 2
    ];

    /**
     * Primitive polynomials for GF(7^n)
     */
    private const GF7_POLYNOMIALS = [
        2 => [1, 0, 3],    // x^2 + 3
        3 => [1, 0, 1, 4], // x^3 + x + 4
    ];

    /**
     * Get the primitive polynomial for GF(prime^exponent)
     *
     * @return int|array<int, int>
     * @throws RuntimeException if no polynomial is available
     */
    public static function get(int $prime, int $exponent): int|array
    {
        if ($prime === 2 && isset(self::GF2_POLYNOMIALS[$exponent])) {
            return self::GF2_POLYNOMIALS[$exponent];
        }

        if ($prime === 3 && isset(self::GF3_POLYNOMIALS[$exponent])) {
            return self::GF3_POLYNOMIALS[$exponent];
        }

        if ($prime === 5 && isset(self::GF5_POLYNOMIALS[$exponent])) {
            return self::GF5_POLYNOMIALS[$exponent];
        }

        if ($prime === 7 && isset(self::GF7_POLYNOMIALS[$exponent])) {
            return self::GF7_POLYNOMIALS[$exponent];
        }

        throw new RuntimeException(
            sprintf('No primitive polynomial available for GF(%d^%d)', $prime, $exponent)
        );
    }

    public static function has(int $prime, int $exponent): bool
    {
        return match ($prime) {
            2 => isset(self::GF2_POLYNOMIALS[$exponent]),
            3 => isset(self::GF3_POLYNOMIALS[$exponent]),
            5 => isset(self::GF5_POLYNOMIALS[$exponent]),
            7 => isset(self::GF7_POLYNOMIALS[$exponent]),
            default => false,
        };
    }
}
