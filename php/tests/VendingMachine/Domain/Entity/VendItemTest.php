<?php

declare(strict_types=1);

namespace Tests\Unit\VendingMachine\Domain\Entity;

use App\VendingMachine\Domain\Entity\VendItem;
use App\VendingMachine\Domain\ValueObject\VendItemSelector;
use PHPUnit\Framework\TestCase;

final class VendItemTest extends TestCase
{
    public function test_water_item_has_water_selector(): void
    {
        $item = VendItem::water();

        self::assertTrue(
            $item->selector()->equals(VendItemSelector::water())
        );
    }

    public function test_juice_item_has_juice_selector(): void
    {
        $item = VendItem::juice();

        self::assertTrue(
            $item->selector()->equals(VendItemSelector::juice())
        );
    }

    public function test_soda_item_has_soda_selector(): void
    {
        $item = VendItem::soda();

        self::assertTrue(
            $item->selector()->equals(VendItemSelector::soda())
        );
    }

    public function test_each_factory_creates_distinct_instances(): void
    {
        $first  = VendItem::water();
        $second = VendItem::water();

        self::assertNotSame($first, $second);
        self::assertTrue(
            $first->selector()->equals($second->selector())
        );
    }

    public function test_selector_is_immutable(): void
    {
        $item = VendItem::juice();
        $selector = $item->selector();

        self::assertTrue(
            $selector->equals(VendItemSelector::juice())
        );
    }
}
