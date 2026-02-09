<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Exception;

final class InvalidModeTransition extends \DomainException
{
    public static function from(string $from, string $to): self
    {
        return new self("Cannot transition from {$from} to {$to}.");
    }
}
