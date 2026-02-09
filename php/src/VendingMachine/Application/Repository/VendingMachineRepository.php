<?php

declare(strict_types=1);

namespace App\VendingMachine\Application\Repository;

use App\VendingMachine\Domain\Aggregate\VendingMachine;
use App\VendingMachine\Domain\Exception\VendingMachineNotFound;

interface VendingMachineRepository
{
    /**
     * @throws VendingMachineNotFound
     */
    public function get(): VendingMachine;

    public function save(VendingMachine $machine): void;
}
