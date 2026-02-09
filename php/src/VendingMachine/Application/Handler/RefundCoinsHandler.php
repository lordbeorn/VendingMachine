<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Handler;

use App\VendingMachine\Application\Command\RefundCoinsCommand;
use App\VendingMachine\Application\Repository\VendingMachineRepository;
use App\VendingMachine\Domain\ValueObject\CoinCollection;

final class RefundCoinsHandler
{
    public function __construct(
        private VendingMachineRepository $repository
    ) {}

    public function __invoke(RefundCoinsCommand $command): CoinCollection
    {
        $machine = $this->repository->get();

        $refundedCoins = $machine->refundInsertedCoins();

        $this->repository->save($machine);

        return $refundedCoins;
    }
}
