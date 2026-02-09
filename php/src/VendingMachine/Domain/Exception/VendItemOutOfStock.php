<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Exception;

use DomainException;

final class VendItemOutOfStock extends DomainException
{
    public static function becauseNoUnitsLeft(): self
    {
        return new self('Vend item is out of stock.');
    }
}
