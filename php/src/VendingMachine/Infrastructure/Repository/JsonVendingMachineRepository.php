<?php

declare(strict_types=1);

namespace App\VendingMachine\Infrastructure\Repository;

use App\VendingMachine\Application\Repository\VendingMachineRepository;
use App\VendingMachine\Domain\Aggregate\VendingMachine;
use App\VendingMachine\Domain\Exception\VendingMachineNotFound;

final class JsonVendingMachineRepository implements VendingMachineRepository
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function exists(): bool
    {
        return file_exists($this->filePath);
    }

    public function get(): VendingMachine
    {
        if (!$this->exists()) {
            throw VendingMachineNotFound::becauseItIsNotInstalled();
        }

        $data = json_decode(
            file_get_contents($this->filePath),
            true,
            flags: JSON_THROW_ON_ERROR
        );

        return VendingMachine::fromArray($data);
    }

    public function save(VendingMachine $machine): void
    {
        file_put_contents(
            $this->filePath,
            json_encode($machine->toArray(), JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }
}
