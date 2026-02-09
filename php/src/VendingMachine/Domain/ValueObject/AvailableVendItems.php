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


    public  function quantityOf(VendItemSelector $selector): int
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

        $new[$selector->value()] = $units;

        return new self($new);
    }


}
