<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Handler\EnterServiceModeHandler;
use App\VendingMachine\Application\Handler\ExitServiceModeHandler;
use App\VendingMachine\Application\Command\EnterServiceModeCommand;
use App\VendingMachine\Application\Command\ExitServiceModeCommand;
use App\VendingMachine\Domain\Exception\OperationNotAllowed;
use App\VendingMachine\Domain\ValueObject\VendItemSelector;

final class ExitServiceModeHandlerTest extends HandlerTestCase
{
    public function test_it_exits_service_mode(): void
    {
        (new EnterServiceModeHandler($this->repository))(new EnterServiceModeCommand());
        (new ExitServiceModeHandler($this->repository))(new ExitServiceModeCommand());

        $this->expectException(OperationNotAllowed::class);

        $this->repository
            ->get()
            ->refillVendItem(VendItemSelector::water(), 1);
    }
}
