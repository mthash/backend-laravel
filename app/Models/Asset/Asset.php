<?php

namespace App\Models\Asset;

use Illuminate\Database\Eloquent\Model;
use App\Models\Historical\HistoryDailyRevenue;
use App\Models\Asset\Algo;
use App\Models\Dashboard\Overview;
use App\Models\Mining\Pool\Pool;
use App\Models\Mining\Block;
use App\Models\Mining\Contract;
use App\Models\Mining\Relayer;

class Asset extends Model
{
    protected $dateFormat = 'U';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'asset';

    protected $fillable = [
        'id', 'algo_id', 'cmc_id', 'logo_url', 'symbol', 'name', 'mineable', 'can_mine', 'total_hashrate', 'hash_invested', 'price_usd', 'block_generation_time', 'block_reward_amount', 'shares', 'last_block_id'
    ];

    static public function calculateExchangeRate(Asset $firstAsset, Asset $secondAsset): float
    {
        return $secondAsset->price_usd == 0 ? 0 : $firstAsset->price_usd / $secondAsset->price_usd;
    }

    public function getCurrentHashrate(): float
    {
        return $this->hash_invested > 0 ? $this->total_hashrate / $this->hash_invested : $this->total_hashrate;
    }

    public function algo()
    {
        return $this->belongsTo(Algo::class, 'algo_id');
    }

    public function pools()
    {
        return $this->hasMany(Pool::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }

    public function wallets()
    {
        return $this->hasMany('App\Models\User\Wallet');
    }

    public function overviews()
    {
        return $this->hasMany(Overview::class);
    }

    public function relayers()
    {
        return $this->hasMany(Relayer::class);
    }

    public function userAssets()
    {
        return $this->hasMany('App\Models\User\Asset');
    }

    public function historyArcades()
    {
        return $this->hasMany('App\Models\Historical\HistoryArcade');
    }

    public function historyAssets()
    {
        return $this->hasMany('App\Models\Historical\HistoryAsset');
    }

    public function historyDailyRevenues()
    {
        return $this->hasMany(HistoryDailyRevenue::class);
    }
}
