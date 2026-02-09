<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\ValueObject;

use App\VendingMachine\Domain\Exception\VendItemNotFound;
use App\VendingMachine\Domain\Exception\VendItemOutOfStock;

final class AvailableVendItems
{
    /**
     * @var array<string,int> selector => quantity
     */
    private array $vendItemsQuantities;

    private function __construct(array $vendItemsQuantities)
    {
        $this->vendItemsQuantities = $vendItemsQuantities;
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public static function emptyCatalog(): self
    {
        return new self([
            VendItemSelector::water()->value() => 0,
            VendItemSelector::juice()->value() => 0,
            VendItemSelector::soda()->value()  => 0,
        ]);
    }

    public static function fromArray(array $items): self
    {
        return new self($items);
    }

    public function toArray(): array
    {
        return $this->vendItemsQuantities;
    }

    public function quantityOf(VendItemSelector $selector): int
    {
        if (!array_key_exists($selector->value(), $this->vendItemsQuantities)) {
            throw VendItemNotFound::fromSelector($selector);
        }

        return $this->vendItemsQuantities[$selector->value()];
    }

    public function vendOne(VendItemSelector $selector): self
    {
        $current = $this->quantityOf($selector);

        if ($current <= 0) {
            throw VendItemOutOfStock::becauseNoUnitsLeft();
        }

        $new = $this->vendItemsQuantities;
        $new[$selector->value()] = $current - 1;

        return new self($new);
    }

    public function refill(VendItemSelector $selector, int $units): self
    {
        if ($units < 0) {
            throw new \DomainException('New value must be greater or equal to 0.');
        }

        $new = $this->vendItemsQuantities;
        $new[$selector->value()] =
            ($new[$selector->value()] ?? 0) + $units;

        return new self($new);
    }
}
