<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Exception;

use App\VendingMachine\Domain\ValueObject\VendItemSelector;

final class VendItemNotFound extends \DomainException
{
    public static function fromSelector(VendItemSelector $selector): self
    {
        return new self(
            sprintf('Vend item "%s" not found.', $selector->value())
        );
    }
}
