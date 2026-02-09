<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Command\SellVendItemCommand;
use App\VendingMachine\Application\Repository\VendingMachineRepository;
use App\VendingMachine\Domain\ValueObject\VendResult;

final class SellVendItemHandler
{
    public function __construct(
        private VendingMachineRepository $repository
    ) {}

    public function __invoke(SellVendItemCommand $command): VendResult
    {
        $machine = $this->repository->get();

        $result = $machine->sellVendItem($command->selector);

        $this->repository->save($machine);

        return $result;
    }
}
