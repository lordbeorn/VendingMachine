<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Infrastructure\Repository;

use PHPUnit\Framework\TestCase;
use App\VendingMachine\Infrastructure\Repository\JsonVendingMachineRepository;
use App\VendingMachine\Domain\Aggregate\VendingMachine;
use App\VendingMachine\Domain\Exception\VendingMachineNotFound;
use App\VendingMachine\Domain\ValueObject\Coin;
use App\VendingMachine\Domain\ValueObject\VendItemSelector;
use App\VendingMachine\Domain\ValueObject\CoinCollection;

final class JsonVendingMachineRepositoryTest extends TestCase
{
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = sys_get_temp_dir() . '/vending_machine_test.json';

        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }

    public function test_it_reports_non_existence_when_file_is_missing(): void
    {
        $repository = new JsonVendingMachineRepository($this->filePath);

        $this->assertFalse($repository->exists());
    }

    public function test_it_throws_when_machine_is_not_installed(): void
    {
        $repository = new JsonVendingMachineRepository($this->filePath);

        $this->expectException(VendingMachineNotFound::class);

        $repository->get();
    }

    public function test_it_persists_and_restores_machine_state(): void
    {
        $repository = new JsonVendingMachineRepository($this->filePath);

        $machine = VendingMachine::install();

        $machine->enterServiceMode();
        $machine->refillVendItem(VendItemSelector::water(), 2);
        $machine->replaceAvailableChange(
            CoinCollection::fromCoins(
                Coin::hundredCents(),
                Coin::hundredCents()
            )
        );
        $machine->exitServiceMode();

        $repository->save($machine);

        $this->assertTrue($repository->exists());

        unset($machine);

        $restored = $repository->get();

        $this->assertInstanceOf(VendingMachine::class, $restored);
    }
}
