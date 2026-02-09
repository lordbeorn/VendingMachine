<?php

declare(strict_types=1);

namespace App\VendingMachine\Domain\Aggregate;

use App\VendingMachine\Domain\ValueObject\AvailableVendItems;
use App\VendingMachine\Domain\ValueObject\Coin;
use App\VendingMachine\Domain\ValueObject\CoinCollection;
use App\VendingMachine\Domain\ValueObject\Mode;
use App\VendingMachine\Domain\ValueObject\VendItemSelector;
use App\VendingMachine\Domain\Exception\OperationNotAllowed;
use App\VendingMachine\Domain\Exception\VendItemOutOfStock;
use App\VendingMachine\Domain\Service\ChangeCalculator;
use App\VendingMachine\Domain\ValueObject\VendResult;

final class VendingMachine
{
    private Mode $mode;
    private CoinCollection $insertedCoins;
    private AvailableVendItems $availableVendItems;
    private CoinCollection $availableChange;
    private ChangeCalculator $changeCalculator;

    private function __construct(
        Mode $mode,
        CoinCollection $insertedCoins,
        AvailableVendItems $availableVendItems,
        CoinCollection $availableChange,
        ChangeCalculator $changeCalculator
    ) {
        $this->mode = $mode;
        $this->insertedCoins = $insertedCoins;
        $this->availableVendItems = $availableVendItems;
        $this->availableChange = $availableChange;
        $this->changeCalculator = $changeCalculator;
    }

    public static function install(): self
    {
        return new self(
            Mode::standBy(),
            CoinCollection::empty(),
            AvailableVendItems::emptyCatalog(),
            CoinCollection::empty(),
            new ChangeCalculator()
        );
    }


    ///////////////////
    /// CLIENT ACTIONS
    ///////////////////


    public function insertCoin(Coin $coin): void
    {
        $this->ensureOperationIsInClientMode();

        $newInsertedCoins = $this->insertedCoins->add($coin);

        $this->insertedCoins = $newInsertedCoins;

    }

    public function sellVendItem(VendItemSelector $selectedVendItem): VendResult
    {
        $this->ensureOperationIsInClientMode();

        if ($this->availableVendItems->quantityOf($selectedVendItem) <= 0) {
            throw VendItemOutOfStock::becauseNoUnitsLeft();
        }

        $price = $selectedVendItem->priceCents();
        $paid  = $this->insertedCoins->totalCents();

        if ($paid < $price) {
            throw new \DomainException('Not enough money.');
        }

        $changeAmount = $paid - $price;

        $temporaryAvailableChange =
            $this->availableChange->merge($this->insertedCoins);

        $changeCoinCollection =
            $this->changeCalculator->calculate(
                $changeAmount,
                $temporaryAvailableChange
            );

        $newAvailableChange =
            $temporaryAvailableChange->subtract($changeCoinCollection);

        $this->insertedCoins   = CoinCollection::empty();
        $this->availableChange = $newAvailableChange;

        $this->availableVendItems =
            $this->availableVendItems->vendOne($selectedVendItem);

        return VendResult::success(
            $selectedVendItem,
            $changeCoinCollection
        );
    }


    public function refundInsertedCoins(): CoinCollection
    {
        $this->ensureOperationIsInClientMode();

        if ($this->insertedCoins->isEmpty()) {
            return CoinCollection::empty();
        }

        $returnedCoins = $this->insertedCoins;

        $this->insertedCoins = CoinCollection::empty();
        $this->mode = $this->mode->toStandBy();

        return $returnedCoins;
    }


    private function ensureOperationIsInClientMode(): void
    {
        if ($this->mode->isStandBy()) {
            $this->mode = $this->mode->toClient();
        }

        if (!$this->mode->isClient()) {
            throw OperationNotAllowed::becauseMachineIsNotInClientMode();
        }
    }


    ///////////////////
    /// SERVICE ACTIONS
    ///////////////////



    public function enterServiceMode(): void
    {
        if (!$this->mode->isStandBy()) {
            throw OperationNotAllowed::becauseMachineIsNotInStandByMode();
        }

        $this-> mode =
            $this->mode->toService();
    }

    public function exitServiceMode(): void
    {
        if (!$this->mode->isService()) {
            throw OperationNotAllowed::becauseMachineIsNotInServiceMode();
        }

        $this->mode = $this->mode->toStandBy();
    }


    public function refillVendItem(VendItemSelector $selector, int $units): void
    {
        if (!$this->mode->isService()) {
            throw OperationNotAllowed::becauseMachineIsNotInServiceMode();
        }

        $this->availableVendItems = $this->availableVendItems->refill($selector, $units);
    }

    public function replaceAvailableChange(CoinCollection $availableChange): void
    {
        if (!$this->mode->isService()) {
            throw OperationNotAllowed::becauseMachineIsNotInServiceMode();
        }

        $this->availableChange = $availableChange;
    }



}
