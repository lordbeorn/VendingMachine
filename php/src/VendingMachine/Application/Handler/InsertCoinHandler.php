<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Command\InsertCoinCommand;
use App\VendingMachine\Application\Repository\VendingMachineRepository;

final class InsertCoinHandler
{
    public function __construct(
        private VendingMachineRepository $repository
    ) {}

    public function __invoke(InsertCoinCommand $command): void
    {
        $machine = $this->repository->get();

        $machine->insertCoin($command->coin);

        $this->repository->save($machine);
    }
}
