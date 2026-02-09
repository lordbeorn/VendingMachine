<?php

declare(strict_types=1);

namespace Tests\Unit\VendingMachine\Domain\ValueObject;

use App\VendingMachine\Domain\ValueObject\VendItemSelector;
use PHPUnit\Framework\TestCase;

final class VendItemSelectorTest extends TestCase
{
    public function test_water_selector_value(): void
    {
        $selector = VendItemSelector::water();

        self::assertSame('WATER', $selector->value());
    }

    public function test_juice_selector_value(): void
    {
        $selector = VendItemSelector::juice();

        self::assertSame('JUICE', $selector->value());
    }

    public function test_soda_selector_value(): void
    {
        $selector = VendItemSelector::soda();

        self::assertSame('SODA', $selector->value());
    }

    public function test_same_selectors_are_equal(): void
    {
        $a = VendItemSelector::water();
        $b = VendItemSelector::water();

        self::assertTrue($a->equals($b));
        self::assertTrue($b->equals($a));
    }

    public function test_different_selectors_are_not_equal(): void
    {
        $water = VendItemSelector::water();
        $juice = VendItemSelector::juice();
        $soda  = VendItemSelector::soda();

        self::assertFalse($water->equals($juice));
        self::assertFalse($water->equals($soda));
        self::assertFalse($juice->equals($soda));
    }

    public function test_equality_is_based_on_value_not_identity(): void
    {
        $first  = VendItemSelector::juice();
        $second = VendItemSelector::juice();

        self::assertNotSame($first, $second);
        self::assertTrue($first->equals($second));
    }
}
