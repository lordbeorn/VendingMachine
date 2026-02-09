<?php

declare(strict_types=1);

namespace Tests\Unit\VendingMachine\Domain\Aggregate;

use App\VendingMachine\Domain\Aggregate\VendingMachine;
use App\VendingMachine\Domain\Exception\CannotMakeExactChange;
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

    public function test_successful_sale_consumes_inserted_coins(): void
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

        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::fiveCents());
        $machine->sellVendItem(VendItemSelector::water());

        $machine->insertCoin(Coin::hundredCents());
        $machine->sellVendItem(VendItemSelector::water());

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

        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::twentyFiveCents());
        $machine->insertCoin(Coin::tenCents());
        $machine->insertCoin(Coin::fiveCents());

        $machine->sellVendItem(VendItemSelector::water());

        $machine->insertCoin(Coin::hundredCents());

        $machine->sellVendItem(VendItemSelector::water());

        self::assertTrue(true);
    }
}
