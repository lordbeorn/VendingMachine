<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Handler\InsertCoinHandler;
use App\VendingMachine\Application\Command\InsertCoinCommand;
use App\VendingMachine\Domain\ValueObject\Coin;

final class InsertCoinHandlerTest extends HandlerTestCase
{
    public function test_it_inserts_a_coin(): void
    {
        $handler = new InsertCoinHandler($this->repository);

        $handler(new InsertCoinCommand(Coin::hundredCents()));

        $returned = $this->repository->get()->refundInsertedCoins();

        $this->assertSame(100, $returned->totalCents());
    }
}
