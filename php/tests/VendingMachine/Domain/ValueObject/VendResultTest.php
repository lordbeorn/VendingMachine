<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Domain\ValueObject;

use App\VendingMachine\Domain\ValueObject\Coin;
use App\VendingMachine\Domain\ValueObject\CoinCollection;
use App\VendingMachine\Domain\ValueObject\VendItemSelector;
use App\VendingMachine\Domain\ValueObject\VendResult;
use PHPUnit\Framework\TestCase;

final class VendResultTest extends TestCase
{
    public function test_success_creates_vend_result_with_item_and_change(): void
    {
        $item = VendItemSelector::water();
        $change = CoinCollection::fromCoins(
            Coin::tenCents(),
            Coin::fiveCents()
        );

        $result = VendResult::success($item, $change);

        self::assertSame($item, $result->item());
        self::assertSame($change, $result->change());
    }

    public function test_change_can_be_empty(): void
    {
        $item = VendItemSelector::juice();
        $change = CoinCollection::empty();

        $result = VendResult::success($item, $change);

        self::assertSame($item, $result->item());
        self::assertTrue($result->change()->isEmpty());
    }

    public function test_vend_result_is_immutable(): void
    {
        $item = VendItemSelector::soda();
        $change = CoinCollection::fromCoins(Coin::fiveCents());

        $result = VendResult::success($item, $change);

        $newChange = $result->change()->add(Coin::fiveCents());

        // Original result is unchanged
        self::assertSame(5, $result->change()->totalCents());
        self::assertSame(10, $newChange->totalCents());
    }
}
