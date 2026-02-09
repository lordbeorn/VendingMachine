<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\ValueObject;

use App\VendingMachine\Domain\Exception\InvalidModeTransition;

final class Mode
{
    private const STAND_BY = 'STAND_BY';
    private const CLIENT   = 'CLIENT';
    private const SERVICE  = 'SERVICE';

    private function __construct(
        private readonly string $value
    ) {}

    public static function standBy(): self
    {
        return new self(self::STAND_BY);
    }

    public static function client(): self
    {
        return new self(self::CLIENT);
    }

    public static function service(): self
    {
        return new self(self::SERVICE);
    }

    public static function fromString(string $value): self
    {
        return match ($value) {
            self::STAND_BY => self::standBy(),
            self::CLIENT   => self::client(),
            self::SERVICE  => self::service(),
            default => throw new \InvalidArgumentException(
                sprintf('Invalid vending machine mode "%s".', $value)
            ),
        };
    }

    public function isStandBy(): bool
    {
        return $this->value === self::STAND_BY;
    }

    public function isClient(): bool
    {
        return $this->value === self::CLIENT;
    }

    public function isService(): bool
    {
        return $this->value === self::SERVICE;
    }

    public function toClient(): self
    {
        if ($this->isStandBy()) {
            return self::client();
        }

        throw InvalidModeTransition::from($this->value, self::CLIENT);
    }

    public function toService(): self
    {
        if ($this->isStandBy()) {
            return self::service();
        }

        throw InvalidModeTransition::from($this->value, self::SERVICE);
    }

    public function toStandBy(): self
    {
        if ($this->isClient() || $this->isService()) {
            return self::standBy();
        }

        throw InvalidModeTransition::from($this->value, self::STAND_BY);
    }

    public function value(): string
    {
        return $this->value;
    }
}
