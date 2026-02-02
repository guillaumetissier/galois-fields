<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields;

use Guillaumetissier\GaloisFields\Exception\InvalidFieldOrderException;
use Guillaumetissier\GaloisFields\Field\GaloisFieldFactory;
use Guillaumetissier\GaloisFields\Field\GaloisFieldInterface;

/**
 * Main entry point for working with Galois fields.
 *
 * Usage:
 *   $gf256 = new GaloisField(256);  // GF(2^8) for QR codes
 *   $gf7 = new GaloisField(7);      // GF(7) prime field
 *   $result = $gf256->multiply(123, 45);
 */
final class GaloisField implements GaloisFieldInterface
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
}
