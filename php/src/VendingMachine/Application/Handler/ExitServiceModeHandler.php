<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Command\ExitServiceModeCommand;
use App\VendingMachine\Application\Repository\VendingMachineRepository;

final class ExitServiceModeHandler
{
    public function __construct(
        private VendingMachineRepository $repository
    ) {}

    public function __invoke(ExitServiceModeCommand $command): void
    {
        $machine = $this->repository->get();

        $machine->exitServiceMode();

        $this->repository->save($machine);
    }
}
