<?php

declare(strict_types=1);

namespace Tests\Unit\VendingMachine\Domain\ValueObject;

use App\VendingMachine\Domain\Exception\VendItemNotFound;
use App\VendingMachine\Domain\Exception\VendItemOutOfStock;
use App\VendingMachine\Domain\ValueObject\AvailableVendItems;
use App\VendingMachine\Domain\ValueObject\VendItemSelector;
use DomainException;
use PHPUnit\Framework\TestCase;

final class AvailableVendItemsTest extends TestCase
{
    public function test_empty_has_no_items(): void
    {
        $available = AvailableVendItems::empty();

        $this->expectException(VendItemNotFound::class);

        $available->quantityOf(VendItemSelector::water());
    }

    public function test_refill_sets_quantity_for_selector(): void
    {
        $available = AvailableVendItems::empty()
            ->refill(VendItemSelector::water(), 5);

        self::assertSame(
            5,
            $available->quantityOf(VendItemSelector::water())
        );
    }

    public function test_refill_does_not_mutate_original_instance(): void
    {
        $original = AvailableVendItems::empty();

        $refilled = $original->refill(VendItemSelector::juice(), 3);

        $this->expectException(VendItemNotFound::class);
        $original->quantityOf(VendItemSelector::juice());

        self::assertSame(
            3,
            $refilled->quantityOf(VendItemSelector::juice())
        );
    }

    public function test_refill_with_non_positive_units_throws(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('New value must be greater or equal to 0.');

        AvailableVendItems::empty()
            ->refill(VendItemSelector::soda(), -1);
    }

    public function test_vend_one_decrements_quantity(): void
    {
        $available = AvailableVendItems::empty()
            ->refill(VendItemSelector::water(), 2);

        $afterVend = $available->vendOne(VendItemSelector::water());

        self::assertSame(2, $available->quantityOf(VendItemSelector::water()));
        self::assertSame(1, $afterVend->quantityOf(VendItemSelector::water()));
    }

    public function test_vend_one_throws_when_item_not_found(): void
    {
        $available = AvailableVendItems::empty();

        $this->expectException(VendItemNotFound::class);

        $available->vendOne(VendItemSelector::juice());
    }

    public function test_vend_one_throws_when_out_of_stock(): void
    {
        $available = AvailableVendItems::empty()
            ->refill(VendItemSelector::soda(), 0);

        $this->expectException(VendItemOutOfStock::class);

        $available->vendOne(VendItemSelector::soda());
    }

    public function test_vending_all_units_leads_to_out_of_stock(): void
    {
        $available = AvailableVendItems::empty()
            ->refill(VendItemSelector::water(), 1);

        $available = $available->vendOne(VendItemSelector::water());

        $this->expectException(VendItemOutOfStock::class);

        $available->vendOne(VendItemSelector::water());
    }

    public function test_empty_catalog_contains_all_items_with_zero_stock(): void
        {
            $catalog = AvailableVendItems::emptyCatalog();

            self::assertSame(
                0,
                $catalog->quantityOf(VendItemSelector::water())
            );

            self::assertSame(
                0,
                $catalog->quantityOf(VendItemSelector::juice())
            );

            self::assertSame(
                0,
                $catalog->quantityOf(VendItemSelector::soda())
            );
        }

        public function test_empty_catalog_does_not_throw_item_not_found(): void
        {
            $catalog = AvailableVendItems::emptyCatalog();

            self::assertIsInt($catalog->quantityOf(VendItemSelector::water()));
            self::assertIsInt($catalog->quantityOf(VendItemSelector::juice()));
            self::assertIsInt($catalog->quantityOf(VendItemSelector::soda()));
        }
}
