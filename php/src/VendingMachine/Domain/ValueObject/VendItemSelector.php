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

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

}
