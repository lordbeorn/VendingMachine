<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Domain\ValueObject;

use App\VendingMachine\Domain\ValueObject\Coin;
use PHPUnit\Framework\TestCase;

final class CoinTest extends TestCase
{
    public function test_it_creates_valid_coins(): void
    {
        $this->assertSame(5, Coin::fiveCents()->cents());
        $this->assertSame(10, Coin::tenCents()->cents());
        $this->assertSame(25, Coin::twentyFiveCents()->cents());
        $this->assertSame(100, Coin::hundredCents()->cents());
    }

    public function test_it_compares_equality(): void
    {
        $this->assertTrue(
            Coin::fiveCents()->equals(Coin::fiveCents())
        );
    }
}
