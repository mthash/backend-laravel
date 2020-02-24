<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction\Transaction;

class TransactionType extends Model
{

    const   P2P         = 1;
    const   MINING      = 2;
    const   FEE         = 3;
    const   BONUS       = 4;
    const   EXCHANGE    = 5;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction_type';

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
