<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Command\RefillVendItemCommand;
use App\VendingMachine\Application\Repository\VendingMachineRepository;

final class RefillVendItemHandler
{
    public function __construct(
        private VendingMachineRepository $repository
    ) {}

    public function __invoke(RefillVendItemCommand $command): void
    {
        $machine = $this->repository->get();

        $machine->refillVendItem(
            $command->selector,
            $command->units
        );

        $this->repository->save($machine);
    }
}
