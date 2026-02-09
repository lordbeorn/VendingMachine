<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Entity;

use App\VendingMachine\Domain\ValueObject\VendItemSelector;

final class VendItem
{
    private function __construct(
        private readonly VendItemSelector $selector
    ) {}

    public static function water(): self
    {
        return new self(VendItemSelector::water());
    }

    public static function juice(): self
    {
        return new self(VendItemSelector::juice());
    }

    public static function soda(): self
    {
        return new self(VendItemSelector::soda());
    }

    public function selector(): VendItemSelector
    {
        return $this->selector;
    }
}
