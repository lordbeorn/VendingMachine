<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Application\Handler;

use PHPUnit\Framework\TestCase;
use App\Tests\VendingMachine\Infrastructure\InMemoryVendingMachineRepository;
use App\VendingMachine\Domain\Aggregate\VendingMachine;

abstract class HandlerTestCase extends TestCase
{
    protected InMemoryVendingMachineRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryVendingMachineRepository();
        $this->repository->save(VendingMachine::install());
    }
}
