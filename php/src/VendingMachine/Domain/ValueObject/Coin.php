<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\ValueObject;

use App\VendingMachine\Domain\Exception\InvalidCoin;

final class Coin
{
    private const VALID_VALUES = [5, 10, 25, 100];

    private function __construct(
        private readonly int $cents
    ) {
        if (!in_array($cents, self::VALID_VALUES, true)) {
            throw InvalidCoin::fromValue($cents);
        }
    }

    public static function fiveCents(): self
    {
        return new self(5);
    }

    public static function tenCents(): self
    {
        return new self(10);
    }

    public static function twentyFiveCents(): self
    {
        return new self(25);
    }

    public static function hundredCents(): self
    {
        return new self(100);
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function equals(self $other): bool
    {
        return $this->cents === $other->cents;
    }
}
