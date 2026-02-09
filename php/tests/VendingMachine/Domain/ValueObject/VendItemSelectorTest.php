<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Domain\ValueObject;

use App\VendingMachine\Domain\ValueObject\VendItemSelector;
use PHPUnit\Framework\Attributes\DataProvider;
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
        self::assertFalse(
            VendItemSelector::water()->equals(VendItemSelector::juice())
        );

        self::assertFalse(
            VendItemSelector::water()->equals(VendItemSelector::soda())
        );

        self::assertFalse(
            VendItemSelector::juice()->equals(VendItemSelector::soda())
        );
    }

    #[DataProvider('priceProvider')]
    public function test_price_cents_is_correct(
        VendItemSelector $selector,
        int $expectedPrice
    ): void {
        self::assertSame($expectedPrice, $selector->priceCents());
    }

    public static function priceProvider(): array
    {
        return [
            'water costs 65 cents' => [VendItemSelector::water(), 65],
            'juice costs 100 cents' => [VendItemSelector::juice(), 100],
            'soda costs 150 cents' => [VendItemSelector::soda(), 150],
        ];
    }

    public function test_price_is_deterministic_for_equal_selectors(): void
    {
        $a = VendItemSelector::juice();
        $b = VendItemSelector::juice();

        self::assertTrue($a->equals($b));
        self::assertSame($a->priceCents(), $b->priceCents());
    }
}
