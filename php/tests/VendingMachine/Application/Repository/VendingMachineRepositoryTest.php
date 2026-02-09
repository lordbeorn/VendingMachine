<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Application\Repository;


use PHPUnit\Framework\TestCase;
use App\VendingMachine\Domain\Aggregate\VendingMachine;
use App\VendingMachine\Domain\Exception\VendingMachineNotFound;
use App\Tests\VendingMachine\Infrastructure\InMemoryVendingMachineRepository;

final class VendingMachineRepositoryTest extends TestCase
{
    private InMemoryVendingMachineRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryVendingMachineRepository();
    }

    public function test_it_throws_when_machine_is_not_installed(): void
    {
        $this->expectException(VendingMachineNotFound::class);

        $this->repository->get();
    }

    public function test_it_returns_the_installed_machine(): void
    {
        $machine = VendingMachine::install();

        $this->repository->save($machine);

        $retrieved = $this->repository->get();

        $this->assertSame($machine, $retrieved);
    }

    public function test_it_overwrites_the_existing_machine(): void
    {
        $firstMachine  = VendingMachine::install();
        $secondMachine = VendingMachine::install();

        $this->repository->save($firstMachine);
        $this->repository->save($secondMachine);

        $retrieved = $this->repository->get();

        $this->assertSame($secondMachine, $retrieved);
    }
}
