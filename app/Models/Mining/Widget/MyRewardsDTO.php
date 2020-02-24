<?php

namespace App\Models\Mining\Widget;

use App\Models\Transaction\TransactionType;
use App\Models\Transaction\Transaction;
use App\Models\User\User;

class MyRewardsDTO
{
    private $blocks = [];

    public function __construct (User $user)
    {
        $transactions = Transaction::where([
            ['status', '>', '0'],
            ['to_user_id', '=', $user->id],
            ['type_id', '=', TransactionType::MINING],
            ['created_at', '>', time() - 3600]
        ])->get();

        if (!$transactions) return;

        foreach ($transactions as $transaction)
        {
            $block  =
                [
                    'age'               => $transaction->block?$transaction->block->created_at:null,
                    'coin'              => $transaction->block?$transaction->block->asset->symbol:null,
                    'percent_reward'    => $transaction->percent,
                    'amount_reward'     => $transaction->amount,
                    'fee'               => 0,
                    'earnings'          => $transaction->amount,
                ];
            $this->blocks[] = $block;
        }
    }

    public function fetch() : array
    {
        return $this->blocks;
    }
}
