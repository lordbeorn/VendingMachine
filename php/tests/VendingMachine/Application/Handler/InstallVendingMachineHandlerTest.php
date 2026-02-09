<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Handler\InstallVendingMachineHandler;
use App\VendingMachine\Application\Command\InstallVendingMachineCommand;
use App\Tests\VendingMachine\Infrastructure\InMemoryVendingMachineRepository;
use App\VendingMachine\Domain\Aggregate\VendingMachine;

final class InstallVendingMachineHandlerTest extends HandlerTestCase
{
    public function test_it_installs_the_machine(): void
    {
        $repository = new InMemoryVendingMachineRepository();
        $handler = new InstallVendingMachineHandler($repository);

        $handler(new InstallVendingMachineCommand());

        $this->assertInstanceOf(VendingMachine::class, $repository->get());
    }
}
