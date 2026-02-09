<?php

declare(strict_types=1);

namespace App\Controller;

use App\VendingMachine\Domain\Aggregate\VendingMachine;
use App\VendingMachine\Domain\ValueObject\Coin;
use App\VendingMachine\Domain\ValueObject\CoinCollection;
use App\VendingMachine\Domain\ValueObject\VendItemSelector;
use App\VendingMachine\Infrastructure\Repository\JsonVendingMachineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class VendingMachineController extends AbstractController
{
    public function index(Request $request): Response
    {
        $repoPath = $this->getParameter('kernel.project_dir') . '/var/vending_machine.json';
        $repo = new JsonVendingMachineRepository($repoPath);

        // Install or restore
        if (!$repo->exists()) {
            $machine = VendingMachine::install();
            $repo->save($machine);
        } else {
            $machine = $repo->get();
        }

        $error = null;
        $message = null;



        try {
            if ($request->isMethod('POST')) {
                $action = (string) $request->request->get('action');

                /* ---------- MODE ---------- */

                if ($action === 'enter_service') {
                    $machine->enterServiceMode();
                    $message = 'Entered service mode.';
                }

                if ($action === 'exit_service') {
                    $machine->exitServiceMode();
                    $message = 'Exited service mode.';
                }

                /* ---------- INSERT COIN ---------- */

                if (str_starts_with($action, 'insert_coin_')) {
                    $value = (int) str_replace('insert_coin_', '', $action);
                    $machine->insertCoin(Coin::fromCents($value));
                    $message = sprintf('Inserted %d¢.', $value);
                }

                /* ---------- REFUND ---------- */

                if ($action === 'refund') {
                    $returned = $machine->refundInsertedCoins();

                    $message = $returned->isEmpty()
                        ? 'No coins to return.'
                        : 'Returned: ' . $this->formatCoins($returned);
                }

                /* ---------- SELL ---------- */

                if (str_starts_with($action, 'sell_')) {
                    $item = VendItemSelector::fromString(
                        str_replace('sell_', '', $action)
                    );

                    $result = $machine->sellVendItem($item);

                    $change = $result->change();
                    $message = sprintf(
                        'Sold %s. Change: %s.',
                        $item->value(),
                        $change->isEmpty() ? 'none' : $this->formatCoins($change)
                    );
                }

                /* ---------- REFILL ---------- */

                if ($action === 'refill') {
                    foreach (['WATER', 'JUICE', 'SODA'] as $item) {
                        $qty = (int) $request->request->get('refill_' . strtolower($item));
                        $machine->refillVendItem(
                            VendItemSelector::fromString($item),
                            $qty
                        );
                    }
                    $message = 'Products refilled.';
                }

                /* ---------- REPLACE CHANGE ---------- */

                if ($action === 'replace_change') {
                    $coins = [];

                    foreach ([5, 10, 25, 100] as $value) {
                        $count = (int) $request->request->get('coin_' . $value);
                        for ($i = 0; $i < $count; $i++) {
                            $coins[] = Coin::fromCents($value);
                        }
                    }

                    $machine->replaceAvailableChange(
                        CoinCollection::fromCoins(...$coins)
                    );

                    $message = 'Available change replaced.';
                }

                $repo->save($machine);
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return $this->render('vending_machine/index.html.twig', [
            'machine_state' => [
                'mode' => match (true) {
                    $machine->isStandBy() => 'STAND_BY',
                    $machine->isService() => 'SERVICE',
                    $machine->isClient()  => 'CLIENT',
                },
                'inserted' => array_map(
                    fn ($coin) => $coin->cents(),
                    $machine->insertedCoins()->coins()
                ),
                'availableChange' => array_map(
                    fn ($coin) => $coin->cents(),
                    $machine->availableChange()->coins()
                ),
                'stock' => $machine->stock()->toArray(),
            ],
            'json' => file_exists($repoPath)
                ? json_decode(file_get_contents($repoPath), true)
                : null,
            'error' => $error,
            'message' => $message,
        ]);


    }

    private function formatCoins(CoinCollection $coins): string
    {
        $counts = [];

        foreach ($coins->coins() as $coin) {
            $counts[$coin->cents()] = ($counts[$coin->cents()] ?? 0) + 1;
        }

        ksort($counts);

        return implode(', ', array_map(
            fn (int $count, int $value) => sprintf('%dx%d¢', $count, $value),
            $counts,
            array_keys($counts)
        ));
    }
}
