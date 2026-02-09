<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\ValueObject;

final class VendResult
{
    private function __construct(
        private readonly VendItemSelector $item,
        private readonly CoinCollection $change
    ) {
    }

    public static function success(
        VendItemSelector $item,
        CoinCollection $change
    ): self {
        return new self($item, $change);
    }

    public function item(): VendItemSelector
    {
        return $this->item;
    }

    public function change(): CoinCollection
    {
        return $this->change;
    }
}
