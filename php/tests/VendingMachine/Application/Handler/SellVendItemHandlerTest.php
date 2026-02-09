<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Handler\EnterServiceModeHandler;
use App\VendingMachine\Application\Handler\ExitServiceModeHandler;
use App\VendingMachine\Application\Handler\InsertCoinHandler;
use App\VendingMachine\Application\Handler\SellVendItemHandler;
use App\VendingMachine\Application\Handler\ReplaceAvailableChangeHandler;
use App\VendingMachine\Application\Command\EnterServiceModeCommand;
use App\VendingMachine\Application\Command\ExitServiceModeCommand;
use App\VendingMachine\Application\Command\InsertCoinCommand;
use App\VendingMachine\Application\Command\SellVendItemCommand;
use App\VendingMachine\Application\Command\ReplaceAvailableChangeCommand;
use App\VendingMachine\Domain\ValueObject\Coin;
use App\VendingMachine\Domain\ValueObject\CoinCollection;
use App\VendingMachine\Domain\ValueObject\VendItemSelector;
use App\VendingMachine\Domain\ValueObject\VendResult;

final class SellVendItemHandlerTest extends HandlerTestCase
{
    public function test_it_sells_a_vend_item(): void
    {
        (new EnterServiceModeHandler($this->repository))(new EnterServiceModeCommand());

        $machine = $this->repository->get();
        $machine->refillVendItem(VendItemSelector::juice(), 1);
        $this->repository->save($machine);

        (new ReplaceAvailableChangeHandler($this->repository))(
            new ReplaceAvailableChangeCommand(
                CoinCollection::fromCoins(Coin::hundredCents())
            )
        );

        (new ExitServiceModeHandler($this->repository))(new ExitServiceModeCommand());

        (new InsertCoinHandler($this->repository))(
            new InsertCoinCommand(Coin::hundredCents())
        );

        $result = (new SellVendItemHandler($this->repository))(
            new SellVendItemCommand(VendItemSelector::juice())
        );

        $this->assertInstanceOf(VendResult::class, $result);
    }
}
