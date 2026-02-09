<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Handler\InsertCoinHandler;
use App\VendingMachine\Application\Handler\RefundCoinsHandler;
use App\VendingMachine\Application\Command\InsertCoinCommand;
use App\VendingMachine\Application\Command\RefundCoinsCommand;
use App\VendingMachine\Domain\ValueObject\Coin;

final class RefundCoinsHandlerTest extends HandlerTestCase
{
    public function test_it_refunds_inserted_coins(): void
    {
        (new InsertCoinHandler($this->repository))(
            new InsertCoinCommand(Coin::hundredCents())
        );

        $coins = (new RefundCoinsHandler($this->repository))(
            new RefundCoinsCommand()
        );

        $this->assertSame(100, $coins->totalCents());
    }
}
