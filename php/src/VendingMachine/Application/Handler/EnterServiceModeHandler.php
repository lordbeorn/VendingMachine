<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Command\EnterServiceModeCommand;
use App\VendingMachine\Application\Repository\VendingMachineRepository;

final class EnterServiceModeHandler
{
    public function __construct(
        private VendingMachineRepository $repository
    ) {}

    public function __invoke(EnterServiceModeCommand $command): void
    {
        $machine = $this->repository->get();

        $machine->enterServiceMode();

        $this->repository->save($machine);
    }
}
