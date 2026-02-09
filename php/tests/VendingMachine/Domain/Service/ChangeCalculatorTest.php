<?php

declare(strict_types=1);

namespace Tests\Unit\VendingMachine\Domain\Service;

use App\VendingMachine\Domain\Exception\CannotMakeExactChange;
use App\VendingMachine\Domain\Service\ChangeCalculator;
use App\VendingMachine\Domain\ValueObject\Coin;
use App\VendingMachine\Domain\ValueObject\CoinCollection;
use PHPUnit\Framework\TestCase;

final class ChangeCalculatorTest extends TestCase
{
    public function test_zero_change_returns_empty_collection(): void
    {
        $calculator = new ChangeCalculator();

        $availableChange = CoinCollection::fromCoins(
            Coin::twentyFiveCents(),
            Coin::tenCents()
        );

        $change = $calculator->calculate(0, $availableChange);

        self::assertTrue($change->isEmpty());
    }

    public function test_calculates_exact_change_with_available_coins(): void
    {
        $calculator = new ChangeCalculator();

        // 65 = 25 + 25 + 10 + 5
        $availableChange = CoinCollection::fromCoins(
            Coin::twentyFiveCents(),
            Coin::twentyFiveCents(),
            Coin::tenCents(),
            Coin::fiveCents()
        );

        $change = $calculator->calculate(65, $availableChange);

        self::assertSame(65, $change->totalCents());
        self::assertCount(4, $change->coins());
    }

    public function test_calculates_change_using_greedy_strategy(): void
    {
        $calculator = new ChangeCalculator();

        // Change: 30 = 25 + 5 (not 10 + 10 + 10)
        $availableChange = CoinCollection::fromCoins(
            Coin::twentyFiveCents(),
            Coin::tenCents(),
            Coin::tenCents(),
            Coin::fiveCents()
        );

        $change = $calculator->calculate(30, $availableChange);

        self::assertSame(30, $change->totalCents());
        self::assertCount(2, $change->coins());
    }

    public function test_calculate_does_not_mutate_available_change(): void
    {
        $calculator = new ChangeCalculator();

        $availableChange = CoinCollection::fromCoins(
            Coin::twentyFiveCents(),
            Coin::tenCents(),
            Coin::fiveCents()
        );

        $calculator->calculate(15, $availableChange);

        // original collection must remain unchanged
        self::assertSame(40, $availableChange->totalCents());
        self::assertCount(3, $availableChange->coins());
    }

    public function test_throws_when_exact_change_cannot_be_made(): void
    {
        $calculator = new ChangeCalculator();

        // Cannot make 15 with only a 10-cent coin
        $availableChange = CoinCollection::fromCoins(
            Coin::tenCents()
        );

        $this->expectException(CannotMakeExactChange::class);

        $calculator->calculate(15, $availableChange);
    }

    public function test_ignores_coins_not_allowed_for_change(): void
    {
        $calculator = new ChangeCalculator();

        // 100-cent coin should be ignored for change
        $availableChange = CoinCollection::fromCoins(
            Coin::hundredCents(),
            Coin::tenCents(),
            Coin::fiveCents()
        );

        $change = $calculator->calculate(15, $availableChange);

        self::assertSame(15, $change->totalCents());
        self::assertCount(2, $change->coins());
    }
}
