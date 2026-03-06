<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\DB;

class SettlementService
{
    public function settle(Event $event, string $result): void
    {
        if ($event->status === 'finished') {
            throw new \Exception('Event already settled.');
        }

        DB::transaction(function () use ($event, $result) {

            $event->update([
                'result' => $result,
                'status' => 'finished',
            ]);

            $bets = $event->bets()
                ->where('status', 'pending')
                ->get();

            foreach ($bets as $bet) {

                if ($bet->selection === $result) {

                    $payout = $event->payoutFor($bet->selection);
                    $winAmount = round($bet->amount * $payout, 2);
                    $profit = $winAmount - $bet->amount;

                    $bet->update([
                        'status' => 'won',
                        'payout_multiplier' => $payout,
                        'payout_amount' => $winAmount,
                        'profit' => $profit,
                    ]);

                    $bet->user->increment('coins', $winAmount);

                } else {

                    $bet->update([
                        'status' => 'lost',
                        'payout_multiplier' => null,
                        'payout_amount' => 0,
                        'profit' => -$bet->amount,
                    ]);
                }
            }
        });
    }
}