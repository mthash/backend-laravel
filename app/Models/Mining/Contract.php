<?php

namespace App\Models\Mining;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asset\Asset;
use App\Models\User\Wallet;
use App\Models\Mining\Pool\Pool;
use App\Models\Mining\Block;

class Contract extends Model
{
    protected $dateFormat = 'U';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contract';

    static public function getUserInvestmentsPerAsset (User $user)
    {
        $request = '
            SELECT `asset_id`, SUM(`amount`) as `hash_invested`
            FROM `contract`
            WHERE `status` > 0 and `user_id` = ' . $user->id . '
            GROUP by `asset_id`
            HAVING SUM(`amount`) > 0
        ';

        $result     = \DB::select($request);

        return $result;
    }

    static public function getUserInvestedHashByAsset (User $user, Asset $asset) : float
    {
        $request    = 'SELECT SUM(`amount`) AS `hash_invested` FROM `contract` WHERE `status` > 0 and `user_id` = ' . $user->id . ' AND `asset_id` = ' . $asset->id;
        $result     = \DB::select($request);
        return (float) $result;
    }

    static public function getUserInvestedHash (User $user) : float
    {
        $request    = 'SELECT SUM(`amount`) AS `hash_invested` FROM `contract` WHERE `status` > 0 and `user_id` = ' . $user->id;
        $result     = \DB::select($request);
        return (float) $result;
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class,'wallet_id');
    }

    public function asset()
    {
        return $this->belongsTo('App\Models\Asset\Asset' ,'asset_id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class,'block_id');
    }

    public function pool()
    {
        return $this->belongsTo(Pool::class,'pool_id');
    }
}
