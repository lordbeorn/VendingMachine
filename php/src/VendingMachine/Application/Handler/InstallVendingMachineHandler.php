<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Command\InstallVendingMachineCommand;
use App\VendingMachine\Application\Repository\VendingMachineRepository;
use App\VendingMachine\Domain\Aggregate\VendingMachine;

final class InstallVendingMachineHandler
{
    public function __construct(
        private VendingMachineRepository $repository
    ) {}

    public function __invoke(InstallVendingMachineCommand $command): void
    {
        $machine = VendingMachine::install();

        $this->repository->save($machine);
    }
}
