<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Command;

use App\VendingMachine\Domain\ValueObject\CoinCollection;

final class ReplaceAvailableChangeCommand
{
    public function __construct(
        public readonly CoinCollection $availableChange
    ) {}
}
