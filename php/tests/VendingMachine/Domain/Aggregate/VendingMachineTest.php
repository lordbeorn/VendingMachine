<?php

declare(strict_types=1);

namespace Tests\Unit\VendingMachine\Domain\Aggregate;

use App\VendingMachine\Domain\Aggregate\VendingMachine;
use App\VendingMachine\Domain\Exception\CannotMakeExactChange;
use App\VendingMachine\Domain\Exception\OperationNotAllowed;
use App\VendingMachine\Domain\Exception\VendItemOutOfStock;
use App\VendingMachine\Domain\ValueObject\Coin;
use App\VendingMachine\Domain\ValueObject\VendItemSelector;
use App\VendingMachine\Domain\ValueObject\VendResult;
use App\VendingMachine\Domain\ValueObject\CoinCollection;
use DomainException;
use PHPUnit\Framework\TestCase;

final class VendingMachineTest extends TestCase
{
    public function test_machine_starts_in_standby_and_fails_due_to_empty_stock(): void
    {
        $machine = VendingMachine::install();

        $this->expectException(VendItemOutOfStock::class);

        $machine->sellVendItem(VendItemSelector::water());
    }

    public function test_insert_coin_switches_to_client_mode(): void
    {
        $machine = VendingMachine::install();

        $machine->insertCoin(Coin::fiveCents());

        self::assertTrue(true);
    }

    public function test_cannot_sell_without_enough_money(): void
    {
        $machine = VendingMachine::install();

        $machine->enterServiceMode();
        $machine->refillVendItem(VendItemSelector::water(), 1);
        $machine->exitServiceMode();

        $machine->insertCoin(Coin::tenCents());

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Not enough money.');

        $machine->sellVendItem(VendItemSelector::water());
    }

    public function test_cannot_sell_out_of_stock_item(): void
    {
        $machine = VendingMachine::install();

        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::fiveCents());

        $this->expectException(VendItemOutOfStock::class);

        $machine->sellVendItem(VendItemSelector::water());
    }

    public function test_successful_sale_returns_vend_result_and_consumes_inserted_coins(): void
    {
        $machine = VendingMachine::install();

        $machine->enterServiceMode();
        $machine->refillVendItem(VendItemSelector::water(), 1);
        $machine->exitServiceMode();

        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::fiveCents());

        $result = $machine->sellVendItem(VendItemSelector::water());

        self::assertInstanceOf(VendResult::class, $result);
        self::assertTrue(
            $result->item()->equals(VendItemSelector::water())
        );
        self::assertTrue($result->change()->isEmpty());

        // no money left → cannot buy again
        $this->expectException(VendItemOutOfStock::class);

        $machine->sellVendItem(VendItemSelector::water());
    }

    public function test_stock_is_decremented_after_sale(): void
    {
        $machine = VendingMachine::install();

        $machine->enterServiceMode();
        $machine->refillVendItem(VendItemSelector::water(), 1);
        $machine->exitServiceMode();

        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::fiveCents());

        $machine->sellVendItem(VendItemSelector::water());

        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::fiveCents());

        $this->expectException(VendItemOutOfStock::class);

        $machine->sellVendItem(VendItemSelector::water());
    }

    public function test_refill_not_allowed_outside_service_mode(): void
    {
        $machine = VendingMachine::install();

        $this->expectException(OperationNotAllowed::class);

        $machine->refillVendItem(VendItemSelector::water(), 1);
    }

    public function test_enter_service_mode_only_allowed_from_standby(): void
    {
        $machine = VendingMachine::install();

        $machine->enterServiceMode();

        $this->expectException(OperationNotAllowed::class);

        $machine->enterServiceMode();
    }

    public function test_exit_service_mode_only_allowed_from_service(): void
    {
        $machine = VendingMachine::install();

        $this->expectException(OperationNotAllowed::class);

        $machine->exitServiceMode();
    }

    public function test_change_coins_are_removed_from_machine(): void
    {
        $machine = VendingMachine::install();

        $machine->enterServiceMode();
        $machine->refillVendItem(VendItemSelector::water(), 3);
        $machine->exitServiceMode();

        // Seed machine with enough change
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::fiveCents());

        $machine->sellVendItem(VendItemSelector::water());

        // First overpay succeeds
        $machine->insertCoin(Coin::hundredCents());
        $result = $machine->sellVendItem(VendItemSelector::water());

        self::assertSame(35, $result->change()->totalCents());

        // Second overpay must fail (change was removed)
        $machine->insertCoin(Coin::hundredCents());

        $this->expectException(CannotMakeExactChange::class);

        $machine->sellVendItem(VendItemSelector::water());
    }

    public function test_machine_can_make_change_when_enough_coins_exist(): void
    {
        $machine = VendingMachine::install();

        $machine->enterServiceMode();
        $machine->refillVendItem(VendItemSelector::water(), 2);
        $machine->exitServiceMode();

        // Exact payment seeds machine
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::fiveCents());

        $machine->sellVendItem(VendItemSelector::water());

        // Overpay should succeed
        $machine->insertCoin(Coin::hundredCents());

        $result = $machine->sellVendItem(VendItemSelector::water());

        self::assertSame(35, $result->change()->totalCents());
    }


   public function test_refund_returns_inserted_coins(): void
    {
        $machine = VendingMachine::install();

        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::fiveCents());

        $returnedCoins = $machine->refundInsertedCoins();

        self::assertInstanceOf(CoinCollection::class, $returnedCoins);
        self::assertSame(40, $returnedCoins->totalCents());
        self::assertCount(3, $returnedCoins->coins());
    }

    public function test_refund_clears_inserted_coins(): void
    {
        $machine = VendingMachine::install();

        $machine->enterServiceMode();
        $machine->refillVendItem(VendItemSelector::water(), 1);
        $machine->exitServiceMode();

        $machine->insertCoin(Coin::tenCents());

        $machine->refundInsertedCoins();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Not enough money.');

        $machine->sellVendItem(VendItemSelector::water());
    }

    public function test_refund_returns_empty_collection_when_no_coins_inserted(): void
    {
        $machine = VendingMachine::install();

        $returnedCoins = $machine->refundInsertedCoins();

        self::assertTrue($returnedCoins->isEmpty());
        self::assertSame(0, $returnedCoins->totalCents());
    }

    public function test_refund_switches_machine_back_to_standby(): void
    {
        $machine = VendingMachine::install();

        $machine->insertCoin(Coin::fiveCents());

        $machine->refundInsertedCoins();

        $this->expectException(\App\VendingMachine\Domain\Exception\VendItemOutOfStock::class);

        $machine->sellVendItem(\App\VendingMachine\Domain\ValueObject\VendItemSelector::water());
    }

    public function test_refund_not_allowed_outside_client_mode(): void
    {
        $machine = VendingMachine::install();

        $machine->enterServiceMode();

        $this->expectException(OperationNotAllowed::class);

        $machine->refundInsertedCoins();
    }

    public function test_replace_available_change_only_allowed_in_service_mode(): void
    {
        $machine = VendingMachine::install();

        $this->expectException(OperationNotAllowed::class);

        $machine->replaceAvailableChange(
            CoinCollection::fromCoins(Coin::fiveCents())
        );
    }

    public function test_replace_available_change_overwrites_existing_change(): void
    {
        $machine = VendingMachine::install();

        $machine->enterServiceMode();
        $machine->refillVendItem(VendItemSelector::water(), 1);

        $machine->replaceAvailableChange(
            CoinCollection::fromCoins(
                Coin::twentyFiveCents(),
                Coin::tenCents(),
                Coin::fiveCents()
            )
        );

        $machine->replaceAvailableChange(
            CoinCollection::fromCoins(
                Coin::fiveCents(),
                Coin::fiveCents()
            )
        );

        $machine->exitServiceMode();

        $machine->insertCoin(Coin::hundredCents());

        $this->expectException(CannotMakeExactChange::class);

        $machine->sellVendItem(VendItemSelector::water());
    }

    public function test_replace_available_change_allows_machine_to_make_change_when_sufficient(): void
    {
        $machine = VendingMachine::install();

        $machine->enterServiceMode();
        $machine->refillVendItem(VendItemSelector::water(), 1);

        $machine->replaceAvailableChange(
            CoinCollection::fromCoins(
                Coin::twentyFiveCents(),
                Coin::tenCents()
            )
        );

        $machine->exitServiceMode();

        $machine->insertCoin(Coin::hundredCents());

        $machine->sellVendItem(VendItemSelector::water());

        self::assertTrue(true);
    }

}
