<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\ValueObject;


final class VendItemSelector
{
    private const WATER = 'WATER';
    private const JUICE = 'JUICE';
    private const SODA  = 'SODA';

    private function __construct(
        private readonly string $value
    ) {}

    public static function water(): self
    {
        return new self(self::WATER);
    }

    public static function juice(): self
    {
        return new self(self::JUICE);
    }

    public static function soda(): self
    {
        return new self(self::SODA);
    }


    public static function fromString(string $value): self
    {
        return match (strtoupper($value)) {
            self::WATER => self::water(),
            self::JUICE => self::juice(),
            self::SODA  => self::soda(),
            default => throw VendItemNotFound::fromString($value),
        };
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function priceCents(): int
    {
        return match ($this->value) {
            self::WATER => 65,
            self::JUICE => 100,
            self::SODA  => 150,
        };
    }

}
