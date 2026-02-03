<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Polynomial;

use Guillaumetissier\GaloisFields\Field\BinaryExtensionField;

/**
 * Formats a polynomial as a human-readable string.
 *
 * Usage :
 *   PolynomialFormatter::toString($p);        // "5x^2 + 3x + 7"
 *   PolynomialFormatter::toAlphaString($p);   // "α^25x^2 + α^25x + α^198"
 */
final class PolynomialFormatter
{
    /**
     * Representation of coefficients as integers
     *
     * Examples :
     *   [5, 3, 7]  →  "5x^2 + 3x + 7"
     *   [1, 0, 1]  →  "x^2 + 1"
     *   [1, 3]     →  "x + 3"
     *   []         →  "0"
     */
    public static function toString(PolynomialInterface $p): string
    {
        if ($p->isZero()) {
            return '0';
        }

        $terms = [];
        foreach ($p->coefficients() as $i => $coeff) {
            if ($coeff === 0) {
                continue;
            }

            $degree = $p->degree() - $i;
            $terms[] = self::formatTerm((string) $coeff, $degree);
        }

        return implode(' + ', $terms);
    }

    /**
     * Representation of coefficients as α^n.
     *
     * only for fields GF(2^n) (BinaryExtensionField).
     *
     * Examples :
     *   [2, 4, 1]  →  "α^1x^2 + α^2x + 1"
     *   [0, 2, 0]  →  "α^1x"
     *
     * @throws \BadMethodCallException si le champ n'est pas un BinaryExtensionField
     */
    public static function toAlphaString(PolynomialInterface $p): string
    {
        if ($p->isZero()) {
            return '0';
        }

        $field = $p->field();

        if (!$field->isBinary()) {
            throw new \BadMethodCallException(
                'toAlphaString() requires a binary extension field GF(2^n)'
            );
        }

        $terms = [];
        foreach ($p->coefficients() as $i => $coeff) {
            if ($coeff === 0) {
                continue;
            }

            $degree      = $p->degree() - $i;
            $alphaCoeff  = $field->toAlphaPower($coeff);
            $terms[]     = self::formatTerm($alphaCoeff, $degree);
        }

        return implode(' + ', $terms);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Format one term : coefficient + puissance de x.
     *
     * Rules :
     *   coeff = "1", degree = 0  →  "1"
     *   coeff = "1", degree = 1  →  "x"
     *   coeff = "1", degree > 1  →  "x^n"
     *   degree = 0               →  coeff
     *   degree = 1               →  "coeff x"
     *   default                  →  "coeff x^n"
     */
    private static function formatTerm(string $coeff, int $degree): string
    {
        return match (true) {
            $degree === 0                        => $coeff,
            $degree === 1 && $coeff === '1'      => 'x',
            $degree === 1                        => "{$coeff}x",
            $coeff === '1'                       => "x^{$degree}",
            default                              => "{$coeff}x^{$degree}",
        };
    }
}
