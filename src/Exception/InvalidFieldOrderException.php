<?php

declare(strict_types=1);

namespace Guillaumetissier\GaloisFields\Exception;

use InvalidArgumentException;

final class InvalidFieldOrderException extends InvalidArgumentException
{
    public static function notPrimePower(int $order): self
    {
        return new self(
            sprintf('Field order %d is not a prime power. GF(q) only exists when q = p^n for prime p.', $order)
        );
    }

    public static function tooSmall(int $order): self
    {
        return new self(
            sprintf('Field order %d is too small. Minimum order is 2.', $order)
        );
    }
}
