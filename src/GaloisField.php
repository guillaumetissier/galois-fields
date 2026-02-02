<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields;

use BadMethodCallException;
use Guillaumetissier\GaloisFields\Exception\InvalidFieldOrderException;
use Guillaumetissier\GaloisFields\Field\BinaryExtensionField;
use Guillaumetissier\GaloisFields\Field\GaloisFieldFactory;
use Guillaumetissier\GaloisFields\Field\GaloisFieldInterface;
use InvalidArgumentException;

/**
 * Main entry point for working with Galois fields.
 *
 * Usage:
 *   $gf256 = new GaloisField(256);  // GF(2^8) for QR codes
 *   $gf7 = new GaloisField(7);      // GF(7) prime field
 *   $result = $gf256->multiply(123, 45);
 */
class GaloisField implements GaloisFieldInterface
{
    private GaloisFieldInterface $field;

    /**
     * Create a Galois field of the given order
     *
     * @throws InvalidFieldOrderException
     */
    public function __construct(int $order)
    {
        $this->field = GaloisFieldFactory::create($order);
    }

    public function getOrder(): int
    {
        return $this->field->getOrder();
    }

    public function getCharacteristic(): int
    {
        return $this->field->getCharacteristic();
    }

    public function getDegree(): int
    {
        return $this->field->getDegree();
    }

    public function add(int $a, int $b): int
    {
        return $this->field->add($a, $b);
    }

    public function subtract(int $a, int $b): int
    {
        return $this->field->subtract($a, $b);
    }

    public function multiply(int $a, int $b): int
    {
        return $this->field->multiply($a, $b);
    }

    public function divide(int $a, int $b): int
    {
        return $this->field->divide($a, $b);
    }

    public function inverse(int $element): int
    {
        return $this->field->inverse($element);
    }

    public function power(int $element, int $exponent): int
    {
        return $this->field->power($element, $exponent);
    }

    public function isValidElement(int $element): bool
    {
        return $this->field->isValidElement($element);
    }

    /**
     * Get information about this field
     *
     * @return array{order: int, characteristic: int, degree: int, notation: string}
     */
    public function getInfo(): array
    {
        return [
            'order' => $this->getOrder(),
            'characteristic' => $this->getCharacteristic(),
            'degree' => $this->getDegree(),
            'notation' => sprintf('GF(%d^%d)', $this->getCharacteristic(), $this->getDegree()),
        ];
    }

    /**
     * Get the underlying field implementation
     */
    public function getImplementation(): GaloisFieldInterface
    {
        return $this->field;
    }

    /**
     * Get the discrete logarithm of an element (for binary extension fields)
     * Returns the power n such that α^n = element
     *
     * @param int $element The field element
     * @return int The power n
     * @throws BadMethodCallException if not a binary extension field
     * @throws InvalidArgumentException if element is 0 or invalid
     */
    public function log(int $element): int
    {
        if (!$this->field instanceof BinaryExtensionField) {
            throw new BadMethodCallException('log() is only available for binary extension fields GF(2^n)');
        }

        return $this->field->log($element);
    }

    /**
     * Get the element α^power (for binary extension fields)
     *
     * @param int $power The power of alpha
     * @return int The field element
     * @throws BadMethodCallException if not a binary extension field
     */
    public function exp(int $power): int
    {
        if (!$this->field instanceof BinaryExtensionField) {
            throw new BadMethodCallException('exp() is only available for binary extension fields GF(2^n)');
        }

        return $this->field->exp($power);
    }

    /**
     * Convert an element to its alpha power representation (for binary extension fields)
     *
     * @param int $element The field element
     * @return string The representation as "α^n" or "0" or "1"
     * @throws BadMethodCallException if not a binary extension field
     */
    public function toAlphaPower(int $element): string
    {
        if (!$this->field instanceof BinaryExtensionField) {
            throw new BadMethodCallException('toAlphaPower() is only available for binary extension fields GF(2^n)');
        }

        return $this->field->toAlphaPower($element);
    }

    /**
     * Convert an alpha power string to its integer element value (for binary extension fields)
     *
     * @param string $alphaPower The alpha power notation (e.g., "α^5", "1", "0")
     * @return int The field element
     * @throws BadMethodCallException if not a binary extension field
     * @throws InvalidArgumentException if format is invalid
     */
    public function fromAlphaPower(string $alphaPower): int
    {
        if (!$this->field instanceof BinaryExtensionField) {
            throw new BadMethodCallException('fromAlphaPower() is only available for binary extension fields GF(2^n)');
        }

        return $this->field->fromAlphaPower($alphaPower);
    }
}
