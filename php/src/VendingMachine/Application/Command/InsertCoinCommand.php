<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Command;

use App\VendingMachine\Domain\ValueObject\Coin;

final class InsertCoinCommand
{
    public function __construct(
        public readonly Coin $coin
    ) {}
}
