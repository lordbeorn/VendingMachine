<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Service;

use App\VendingMachine\Domain\Exception\CannotMakeExactChange;
use App\VendingMachine\Domain\ValueObject\Coin;
use App\VendingMachine\Domain\ValueObject\CoinCollection;

final class ChangeCalculator
{
    public function calculate(
        int $changeToReturnCents,
        CoinCollection $availableChange
    ): CoinCollection {
        if ($changeToReturnCents === 0) {
            return CoinCollection::empty();
        }

        // Only coins allowed for change
        $denominations = [25, 10, 5];

        $remaining = $changeToReturnCents;
        $availableCoins = $availableChange->coins();
        $changeCoins = [];

        foreach ($denominations as $coinValue) {
            foreach ($availableCoins as $index => $coin) {
                if ($coin->cents() !== $coinValue) {
                    continue;
                }

                if ($remaining < $coinValue) {
                    break;
                }

                $changeCoins[] = $coin;
                $remaining -= $coinValue;

                unset($availableCoins[$index]);
            }
        }

        if ($remaining !== 0) {
            throw CannotMakeExactChange::becauseCannotReturnExactChange();
        }

        return CoinCollection::fromCoins(...$changeCoins);
    }
}
