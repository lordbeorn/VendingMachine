<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Command;

use App\VendingMachine\Domain\ValueObject\VendItemSelector;

final class RefillVendItemCommand
{
    public function __construct(
        public readonly VendItemSelector $selector,
        public readonly int $units
    ) {}
}
