<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Polynomial;

use Guillaumetissier\BitString\BitStringImmutable;
use Guillaumetissier\BitString\BitStringInterface;
use Guillaumetissier\GaloisFields\Field\GaloisFieldInterface;

/**
 * Converts between BitString codeword sequences and polynomials over GF(2^n).
 *
 * The codeword width is derived automatically from the field degree:
 *   GF(2^8)  → 8-bit codewords
 *   GF(2^16) → 16-bit codewords, etc.
 *
 * Codeword ordering follows the standard QR / Reed-Solomon convention:
 * the first codeword is the highest-degree coefficient.
 *
 *   codewords [c0, c1, c2, …, cn]  →  c0*x^n + c1*x^(n-1) + … + cn
 *
 * Usage:
 *   $converter = new PolynomialConverter($gf256);
 *   $poly      = $converter->fromBitString($bitString);
 *   $bits      = $converter->toBitString($poly);
 */
class PolynomialConverter
{
    private GaloisFieldInterface $field;

    /** Bits per codeword, derived from field degree */
    private int $bitsPerCoefficient;

    public function __construct(GaloisFieldInterface $field)
    {
        if (!$field->isBinary()) {
            throw new \InvalidArgumentException(
                'PolynomialConverter requires a binary extension field GF(2^n)'
            );
        }

        $this->field = $field;
        $this->bitsPerCoefficient = $field->getDegree();
    }

    /**
     * Build a polynomial from a BitString.
     *
     * @throws \InvalidArgumentException if the bit string length is not a
     *         multiple of the codeword width
     */
    public function fromBitString(BitStringInterface $bitString): PolynomialImmutable
    {
        return $this->fromBinaryString($bitString->toString());
    }

    /**
     * Build a polynomial from a raw binary string (e.g. '1011010100110010').
     *
     * @throws \InvalidArgumentException if the bit string length is not a
     *         multiple of the codeword width
     */
    public function fromBinaryString(string $binary): PolynomialImmutable
    {
        $length = strlen($binary);

        if ($length % $this->bitsPerCoefficient !== 0) {
            throw new \InvalidArgumentException(
                "Bit string length ($length) is not a multiple of "
                . "codeword width ({$this->bitsPerCoefficient}, derived from field degree)"
            );
        }

        $coefficients = [];
        $chunkCount = intdiv($length, $this->bitsPerCoefficient);

        for ($i = 0; $i < $chunkCount; $i++) {
            $chunk = substr($binary, $i * $this->bitsPerCoefficient, $this->bitsPerCoefficient);
            $coefficients[] = (int)bindec($chunk);
        }

        return PolynomialImmutable::fromCoefficients($this->field, $coefficients);
    }

    /**
     * Convert a polynomial to a BitStringImmutable.
     * Each coefficient is zero-padded to the codeword width, highest degree first.
     */
    public function toBitString(PolynomialInterface $polynomial): BitStringImmutable
    {
        return BitStringImmutable::fromString($this->toBinaryString($polynomial));
    }

    /**
     * Convert a polynomial to a raw binary string (e.g. '1011010100110010').
     * Each coefficient is zero-padded to the codeword width, highest degree first.
     */
    public function toBinaryString(PolynomialInterface $polynomial): string
    {
        $parts = [];

        for ($deg = $polynomial->degree(); $deg >= 0; $deg--) {
            $parts[] = str_pad(decbin($polynomial->coefficientAt($deg)), $this->bitsPerCoefficient, '0', STR_PAD_LEFT);
        }

        return implode('', $parts);
    }
}
