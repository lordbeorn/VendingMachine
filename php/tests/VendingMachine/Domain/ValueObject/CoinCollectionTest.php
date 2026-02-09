<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Domain\ValueObject;

use App\VendingMachine\Domain\ValueObject\Coin;
use App\VendingMachine\Domain\ValueObject\CoinCollection;
use DomainException;
use PHPUnit\Framework\TestCase;

final class CoinCollectionTest extends TestCase
{
    public function test_empty_collection_is_empty(): void
    {
        $collection = CoinCollection::empty();

        self::assertTrue($collection->isEmpty());
        self::assertSame(0, $collection->totalCents());
        self::assertSame([], $collection->coins());
    }

    public function test_add_returns_new_collection_and_does_not_mutate_original(): void
    {
        $coin = Coin::fiveCents();

        $original = CoinCollection::empty();
        $updated  = $original->add($coin);

        self::assertTrue($original->isEmpty());
        self::assertFalse($updated->isEmpty());
        self::assertSame(5, $updated->totalCents());
    }

    public function test_add_multiple_coins(): void
    {
        $collection = CoinCollection::empty()
            ->add(Coin::fiveCents())
            ->add(Coin::tenCents());

        self::assertSame(15, $collection->totalCents());
        self::assertCount(2, $collection->coins());
    }

    public function test_subtract_removes_matching_coins(): void
    {
        $five = Coin::fiveCents();
        $ten  = Coin::tenCents();

        $collection = CoinCollection::empty()
            ->add($five)
            ->add($ten)
            ->add($five);

        $toSubtract = CoinCollection::empty()
            ->add($five);

        $remaining = $collection->subtract($toSubtract);

        self::assertSame(20, $collection->totalCents(), 'Original collection must not change');
        self::assertSame(20 - 5, $remaining->totalCents());
        self::assertCount(2, $remaining->coins());
    }

    public function test_subtract_multiple_coins(): void
    {
        $collection = CoinCollection::empty()
            ->add(Coin::fiveCents())
            ->add(Coin::tenCents())
            ->add(Coin::twentyfiveCents());

        $toSubtract = CoinCollection::empty()
            ->add(Coin::fiveCents())
            ->add(Coin::tenCents());

        $remaining = $collection->subtract($toSubtract);

        self::assertSame(25, $remaining->totalCents());
        self::assertCount(1, $remaining->coins());
    }

    public function test_subtract_throws_if_coin_not_present(): void
    {
        $collection = CoinCollection::empty()
            ->add(Coin::fiveCents());

        $toSubtract = CoinCollection::empty()
            ->add(Coin::tenCents());

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cannot subtract coins that are not present.');

        $collection->subtract($toSubtract);
    }

    public function test_subtract_throws_if_not_enough_matching_coins(): void
    {
        $collection = CoinCollection::empty()
            ->add(Coin::fiveCents());

        $toSubtract = CoinCollection::empty()
            ->add(Coin::fiveCents())
            ->add(Coin::fiveCents());

        $this->expectException(DomainException::class);

        $collection->subtract($toSubtract);
    }

    public function test_is_empty_after_subtracting_all_coins(): void
    {
        $collection = CoinCollection::empty()
            ->add(Coin::fiveCents());

        $remaining = $collection->subtract(
            CoinCollection::empty()->add(Coin::fiveCents())
        );

        self::assertTrue($remaining->isEmpty());
        self::assertSame(0, $remaining->totalCents());
    }

public function test_merge_two_empty_collections_results_in_empty(): void
    {
        $a = CoinCollection::empty();
        $b = CoinCollection::empty();

        $merged = $a->merge($b);

        self::assertTrue($merged->isEmpty());
    }

    public function test_merge_empty_with_non_empty_collection(): void
    {
        $coins = CoinCollection::empty()
            ->add(Coin::fiveCents())
            ->add(Coin::tenCents());

        $merged = CoinCollection::empty()->merge($coins);

        self::assertSame(15, $merged->totalCents());
        self::assertCount(2, $merged->coins());
    }

    public function test_merge_non_empty_with_empty_collection(): void
    {
        $coins = CoinCollection::empty()
            ->add(Coin::twentyFiveCents());

        $merged = $coins->merge(CoinCollection::empty());

        self::assertSame(25, $merged->totalCents());
        self::assertCount(1, $merged->coins());
    }

    public function test_merge_combines_all_coins(): void
    {
        $a = CoinCollection::empty()
            ->add(Coin::fiveCents())
            ->add(Coin::tenCents());

        $b = CoinCollection::empty()
            ->add(Coin::twentyFiveCents())
            ->add(Coin::fiveCents());

        $merged = $a->merge($b);

        self::assertSame(45, $merged->totalCents());
        self::assertCount(4, $merged->coins());
    }

    public function test_merge_does_not_mutate_original_collections(): void
    {
        $a = CoinCollection::empty()->add(Coin::fiveCents());
        $b = CoinCollection::empty()->add(Coin::tenCents());

        $merged = $a->merge($b);

        self::assertSame(5, $a->totalCents());
        self::assertSame(10, $b->totalCents());
        self::assertSame(15, $merged->totalCents());
    }

    public function test_merge_preserves_duplicate_coins(): void
    {
        $a = CoinCollection::empty()
            ->add(Coin::fiveCents());

        $b = CoinCollection::empty()
            ->add(Coin::fiveCents());

        $merged = $a->merge($b);

        self::assertCount(2, $merged->coins());
        self::assertSame(10, $merged->totalCents());
    }


 public function test_from_coins_creates_collection_with_given_coins(): void
    {
        $collection = CoinCollection::fromCoins(
            Coin::fiveCents(),
            Coin::tenCents(),
            Coin::twentyFiveCents()
        );

        self::assertCount(3, $collection->coins());
    }

    public function test_from_coins_total_cents_is_sum_of_given_coins(): void
    {
        $collection = CoinCollection::fromCoins(
            Coin::twentyFiveCents(),
            Coin::twentyFiveCents(),
            Coin::tenCents(),
            Coin::fiveCents()
        );

        self::assertSame(65, $collection->totalCents());
    }

    public function test_from_coins_does_not_mutate_original_instances(): void
    {
        $coin = Coin::tenCents();

        $collection = CoinCollection::fromCoins($coin);

        $newCollection = $collection->add(Coin::fiveCents());

        self::assertCount(1, $collection->coins());
        self::assertCount(2, $newCollection->coins());
    }

    public function test_from_coins_can_be_merged_with_another_collection(): void
    {
        $a = CoinCollection::fromCoins(
            Coin::fiveCents(),
            Coin::tenCents()
        );

        $b = CoinCollection::fromCoins(
            Coin::twentyFiveCents()
        );

        $merged = $a->merge($b);

        self::assertSame(40, $merged->totalCents());
    }

    public function test_from_coins_can_subtract_another_collection(): void
    {
        $collection = CoinCollection::fromCoins(
            Coin::fiveCents(),
            Coin::tenCents(),
            Coin::twentyFiveCents()
        );

        $toSubtract = CoinCollection::fromCoins(
            Coin::tenCents()
        );

        $result = $collection->subtract($toSubtract);

        self::assertSame(30, $result->totalCents());
        self::assertCount(2, $result->coins());
    }

    public function test_subtracting_coin_not_present_throws_exception(): void
    {
        $collection = CoinCollection::fromCoins(
            Coin::fiveCents()
        );

        $toSubtract = CoinCollection::fromCoins(
            Coin::tenCents()
        );

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cannot subtract coins that are not present.');

        $collection->subtract($toSubtract);
    }

    public function test_from_coins_with_no_arguments_creates_empty_collection(): void
    {
        $collection = CoinCollection::fromCoins();

        self::assertTrue($collection->isEmpty());
        self::assertSame(0, $collection->totalCents());
    }

}
