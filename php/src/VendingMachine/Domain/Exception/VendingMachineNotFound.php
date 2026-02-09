<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Exception;

use RuntimeException;

final class VendingMachineNotFound extends RuntimeException
{
    public static function becauseItIsNotInstalled(): self
    {
        return new self('Vending machine is not installed.');
    }
}
