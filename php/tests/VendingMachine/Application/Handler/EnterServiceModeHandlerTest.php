<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Handler\EnterServiceModeHandler;
use App\VendingMachine\Application\Command\EnterServiceModeCommand;
use App\VendingMachine\Domain\ValueObject\VendItemSelector;

final class EnterServiceModeHandlerTest extends HandlerTestCase
{
    public function test_it_enters_service_mode(): void
    {
        $handler = new EnterServiceModeHandler($this->repository);
        $handler(new EnterServiceModeCommand());

        // Would throw if not in service mode
        $this->repository
            ->get()
            ->refillVendItem(VendItemSelector::water(), 1);

        $this->assertTrue(true);
    }
}
