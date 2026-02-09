<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Command\ReplaceAvailableChangeCommand;
use App\VendingMachine\Application\Repository\VendingMachineRepository;

final class ReplaceAvailableChangeHandler
{
    public function __construct(
        private VendingMachineRepository $repository
    ) {}

    public function __invoke(ReplaceAvailableChangeCommand $command): void
    {
        $machine = $this->repository->get();

        $machine->replaceAvailableChange($command->availableChange);

        $this->repository->save($machine);
    }
}
