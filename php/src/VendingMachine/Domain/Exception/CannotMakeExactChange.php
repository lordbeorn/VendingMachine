<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Exception;

use DomainException;

final class CannotMakeExactChange extends DomainException
{
    public static function becauseCannotReturnExactChange(): self
    {
        return new self('Cannot make exact change with available coins.');
    }
}
