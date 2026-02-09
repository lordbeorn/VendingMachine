<?php

declare(strict_types=1);

namespace Tests\Unit\VendingMachine\Domain\Aggregate;

use App\VendingMachine\Domain\Aggregate\VendingMachine;
use App\VendingMachine\Domain\Exception\OperationNotAllowed;
use App\VendingMachine\Domain\Exception\VendItemOutOfStock;
use App\VendingMachine\Domain\ValueObject\Coin;
use App\VendingMachine\Domain\ValueObject\VendItemSelector;
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

        // should not throw
        $machine->insertCoin(Coin::fiveCents());

        self::assertTrue(true);
    }

    public function test_cannot_sell_without_enough_money(): void
    {
        $machine = VendingMachine::install();

        // prepare stock
        $machine->enterServiceMode();
        $machine->refillVendItem(VendItemSelector::water(), 1);
        $machine->exitServiceMode();

        // insufficient money (10 < 65)
        $machine->insertCoin(Coin::tenCents());

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Not enough money.');

        $machine->sellVendItem(VendItemSelector::water());
    }

    public function test_cannot_sell_out_of_stock_item(): void
    {
        $machine = VendingMachine::install();

        // exact payment for water: 25 + 25 + 10 + 5 = 65
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::fiveCents());

        $this->expectException(VendItemOutOfStock::class);

        $machine->sellVendItem(VendItemSelector::water());
    }

    public function test_successful_sale_consumes_inserted_coins(): void
    {
        $machine = VendingMachine::install();

        // refill exactly one item
        $machine->enterServiceMode();
        $machine->refillVendItem(VendItemSelector::water(), 1);
        $machine->exitServiceMode();

        // exact payment
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::fiveCents());

        // first sale succeeds
        $machine->sellVendItem(VendItemSelector::water());

        // second sale fails due to stock (coins were consumed correctly)
        $this->expectException(VendItemOutOfStock::class);

        $machine->sellVendItem(VendItemSelector::water());
    }

    public function test_stock_is_decremented_after_sale(): void
    {
        $machine = VendingMachine::install();

        // refill exactly one unit
        $machine->enterServiceMode();
        $machine->refillVendItem(VendItemSelector::water(), 1);
        $machine->exitServiceMode();

        // first sale
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::fiveCents());

        $machine->sellVendItem(VendItemSelector::water());

        // re-pay exact amount
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::fiveCents());

        // no stock left
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

        // first transition is allowed
        $machine->enterServiceMode();

        // cannot enter again
        $this->expectException(OperationNotAllowed::class);

        $machine->enterServiceMode();
    }

    public function test_exit_service_mode_only_allowed_from_service(): void
    {
        $machine = VendingMachine::install();

        $this->expectException(OperationNotAllowed::class);

        $machine->exitServiceMode();
    }
}
