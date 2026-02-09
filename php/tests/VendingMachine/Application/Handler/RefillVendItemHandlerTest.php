<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Handler\EnterServiceModeHandler;
use App\VendingMachine\Application\Handler\ExitServiceModeHandler;
use App\VendingMachine\Application\Handler\RefillVendItemHandler;
use App\VendingMachine\Application\Command\EnterServiceModeCommand;
use App\VendingMachine\Application\Command\ExitServiceModeCommand;
use App\VendingMachine\Application\Command\RefillVendItemCommand;
use App\VendingMachine\Domain\ValueObject\VendItemSelector;

final class RefillVendItemHandlerTest extends HandlerTestCase
{
    public function test_it_refills_a_vend_item(): void
    {

        (new EnterServiceModeHandler($this->repository))(
            new EnterServiceModeCommand()
        );


        (new RefillVendItemHandler($this->repository))(
            new RefillVendItemCommand(VendItemSelector::water(), 1)
        );

        (new ExitServiceModeHandler($this->repository))(
            new ExitServiceModeCommand()
        );

        $this->assertTrue(true);
    }
}
