<?php

namespace App;

use App\Models\Asset\Asset;
use App\Models\Mining\Contract;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mining\Block;
use App\Models\Asset\AssetRepository;

class Relayer extends Model
{
    protected $dateFormat = 'U';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'relayer';

    public static function recalculateForAsset (Asset $asset) : void
    {
        $investors  = AssetRepository::getInvestorsInvestment($asset);
        foreach ($investors as $investor)
        {
            $hashrate   = $investor->hash_invested * $asset->total_hashrate / $asset->hash_invested;

            $relayer    = new Relayer();
            $relayer->hashrate  = $hashrate;
            $relayer->user_id   = $investor->user_id;
            $relayer->asset_id  = $asset->id;
            $relayer->block_id  = $asset->last_block_id;
            $relayer->save();
        }
    }

    static public function getUserCurrentHashrate (User $user, Asset $asset) : float
    {
        $contract = Contract::where([
            ['user_id', '=', $user->id],
            ['asset_id', '=', $asset->id]
        ])->orderBy('id', 'DESC')->first();
        return (float) $contract? $contract->hashrate: 0;
    }

    public function block()
    {
        return $this->belongsTo(Block::class,'block_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function asset()
    {
        return $this->belongsTo('App\Models\Asset\Asset' ,'asset_id');
    }
}
