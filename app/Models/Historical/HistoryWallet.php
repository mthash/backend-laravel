<?php

namespace App\Models\Historical;

use App\Models\Asset\Asset;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;
use App\Models\User\Wallet;

class HistoryWallet extends Model
{
    const SECONDS_IN_DAY = 3600;

    protected $dateFormat = 'U';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'history_wallet';

    public static  function changeToDay(User $user, Asset $asset)
    {
        $record = self::where([
            ['status', '>', '0'],
            ['user_id', '=', $user->id],
            ['created_at', '>', time() - self::SECONDS_IN_DAY],
            ['asset_id', '=', $asset->id]
        ])->orderBy('id', 'ASC')->first();

        return $record;
    }

    static public function walletChangeToDay(User $user, Wallet $wallet): ?self
    {
        $record = self::where([
            ['status', '>', '0'],
            ['user_id', '=', $user->id],
            ['created_at', '>=', time() - self::SECONDS_IN_DAY],
            ['wallet_id', '=', $wallet->id],
        ])
            ->orderBy('id', 'ASC')
            ->first();

        return !is_bool($record) ? $record : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }

}
