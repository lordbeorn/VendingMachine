<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Exception;

final class OperationNotAllowed extends \DomainException
{
    public static function becauseMachineIsNotInClientMode(): self
    {
        return new self('Not in Client mode. This operation is not allowed.');
    }

    public static function becauseMachineIsNotInServiceMode(): self
    {
        return new self('Not in Service mode. This operation is not allowed.');
    }

    public static function becauseMachineIsNotInStandByMode(): self
    {
        return new self('Machine is not in standby mode.');
    }
}
