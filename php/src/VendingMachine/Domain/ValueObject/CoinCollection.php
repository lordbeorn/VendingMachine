<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\ValueObject;

use DomainException;

final class CoinCollection
{
    /** @var Coin[] */
    private array $coins;

    private function __construct(array $coins)
    {
        $this->coins = $coins;
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function add(Coin $coin): self
    {
        $new = $this->coins;
        $new[] = $coin;

        return new self($new);
    }

    public function subtract(self $other): self
    {
        $remaining = $this->coins;

        foreach ($other->coins as $coinToRemove) {
            $found = false;

            foreach ($remaining as $index => $coin) {
                if ($coin->equals($coinToRemove)) {
                    unset($remaining[$index]);
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw new DomainException('Cannot subtract coins that are not present.');
            }
        }

        return new self(array_values($remaining));
    }

    public function merge(self $other): self
    {
        return new self(array_merge($this->coins, $other->coins));
    }


    public function totalCents(): int
    {
        $sum = 0;

        foreach ($this->coins as $coin) {
            $sum += $coin->cents();
        }

        return $sum;
    }

    public function coins(): array
    {
        return $this->coins;
    }

    public function isEmpty(): bool
    {
        return $this->coins === [];
    }
}
