<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Handler\EnterServiceModeHandler;
use App\VendingMachine\Application\Handler\ReplaceAvailableChangeHandler;
use App\VendingMachine\Application\Command\EnterServiceModeCommand;
use App\VendingMachine\Application\Command\ReplaceAvailableChangeCommand;
use App\VendingMachine\Domain\ValueObject\Coin;
use App\VendingMachine\Domain\ValueObject\CoinCollection;

final class ReplaceAvailableChangeHandlerTest extends HandlerTestCase
{
    public function test_it_replaces_available_change(): void
    {
        (new EnterServiceModeHandler($this->repository))(new EnterServiceModeCommand());

        $handler = new ReplaceAvailableChangeHandler($this->repository);
        $handler(new ReplaceAvailableChangeCommand(
            CoinCollection::fromCoins(
                Coin::hundredCents(),
                Coin::hundredCents()
            )
        ));

        $this->assertTrue(true); // no exception means success
    }
}
