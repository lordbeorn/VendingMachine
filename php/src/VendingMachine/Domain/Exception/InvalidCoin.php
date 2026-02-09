<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Exception;

use DomainException;

final class InvalidCoin extends DomainException
{
    public static function fromValue(float $value): self
    {
        return new self("Invalid coin value: {$value}");
    }
}
