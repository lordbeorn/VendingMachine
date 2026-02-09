<?php

declare(strict_types=1);

namespace App\Tests\VendingMachine\Domain\ValueObject;

use App\VendingMachine\Domain\Exception\InvalidModeTransition;
use App\VendingMachine\Domain\ValueObject\Mode;
use PHPUnit\Framework\TestCase;

final class ModeTest extends TestCase
{
    public function test_standby_mode_flags(): void
    {
        $mode = Mode::standBy();

        self::assertTrue($mode->isStandBy());
        self::assertFalse($mode->isClient());
        self::assertFalse($mode->isService());
        self::assertSame('STAND_BY', $mode->value());
    }

    public function test_client_mode_flags(): void
    {
        $mode = Mode::client();

        self::assertTrue($mode->isClient());
        self::assertFalse($mode->isStandBy());
        self::assertFalse($mode->isService());
        self::assertSame('CLIENT', $mode->value());
    }

    public function test_service_mode_flags(): void
    {
        $mode = Mode::service();

        self::assertTrue($mode->isService());
        self::assertFalse($mode->isStandBy());
        self::assertFalse($mode->isClient());
        self::assertSame('SERVICE', $mode->value());
    }

    public function test_standby_can_transition_to_client(): void
    {
        $standBy = Mode::standBy();

        $client = $standBy->toClient();

        self::assertTrue($client->isClient());
        self::assertTrue($standBy->isStandBy(), 'Original mode must be immutable');
    }

    public function test_standby_can_transition_to_service(): void
    {
        $standBy = Mode::standBy();

        $service = $standBy->toService();

        self::assertTrue($service->isService());
        self::assertTrue($standBy->isStandBy());
    }

    public function test_client_can_transition_to_standby(): void
    {
        $client = Mode::client();

        $standBy = $client->toStandBy();

        self::assertTrue($standBy->isStandBy());
        self::assertTrue($client->isClient());
    }

    public function test_service_can_transition_to_standby(): void
    {
        $service = Mode::service();

        $standBy = $service->toStandBy();

        self::assertTrue($standBy->isStandBy());
        self::assertTrue($service->isService());
    }

    public function test_client_cannot_transition_to_client(): void
    {
        $this->expectException(InvalidModeTransition::class);

        Mode::client()->toClient();
    }

    public function test_client_cannot_transition_to_service(): void
    {
        $this->expectException(InvalidModeTransition::class);

        Mode::client()->toService();
    }

    public function test_service_cannot_transition_to_client(): void
    {
        $this->expectException(InvalidModeTransition::class);

        Mode::service()->toClient();
    }

    public function test_service_cannot_transition_to_service(): void
    {
        $this->expectException(InvalidModeTransition::class);

        Mode::service()->toService();
    }

    public function test_standby_cannot_transition_to_standby(): void
    {
        $this->expectException(InvalidModeTransition::class);

        Mode::standBy()->toStandBy();
    }
}
