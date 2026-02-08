<?php

declare(strict_types=1);

namespace Tests\Unit\VendingMachine\Domain\ValueObject;

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
}
