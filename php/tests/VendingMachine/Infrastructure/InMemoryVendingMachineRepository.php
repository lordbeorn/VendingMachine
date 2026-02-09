<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Infrastructure;

use App\VendingMachine\Application\Repository\VendingMachineRepository;
use App\VendingMachine\Domain\Aggregate\VendingMachine;
use App\VendingMachine\Domain\Exception\VendingMachineNotFound;

final class InMemoryVendingMachineRepository implements VendingMachineRepository
{
    private ?VendingMachine $machine = null;

    public function get(): VendingMachine
    {
        if ($this->machine === null) {
            throw VendingMachineNotFound::becauseItIsNotInstalled();
        }

        return $this->machine;
    }

    public function save(VendingMachine $machine): void
    {
        $this->machine = $machine;
    }
}
