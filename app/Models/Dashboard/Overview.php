<?php

namespace App\Models\Dashboard;

use App\Models\Mining\Pool\Pool;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asset\Asset;

class Overview extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'overview';

    public $timestamps = false;

    protected $fillable = [
        //'*',
        'id', 'asset_id', 'daily_revenue'
    ];

    public function getStaticData() : array
    {
        return
            [
                'daily_revenue'     => $this->getStaticDailyRevenuePerAsset(),
                'power'             => $this->getPowerPerPool(),
            ];
    }

    public function getStaticDailyRevenuePerAsset() : array
    {
        $assets = Asset::where([
            ['status', '>', '0'],
            ['mineable', '=', '1']
        ])->get();
        $return = [];

        foreach ($assets as $asset)
        {
            $revenue = Overview::where('asset_id', '=', $asset->id)->firstOrFail()->daily_revenue;
            $return[] = [
                'id'        => $asset->id,
                'symbol'    => $asset->symbol,
                'revenue'   => $revenue,
            ];
        }

        return $return;
    }

    public function getPowerPerPool() : array
    {
        $return = [];
        foreach (Pool::all() as $pool)
        {
            $return[] = ['id' => $pool->id, 'name' => $pool->name, 'power' => $pool->used_power, 'hashrate' => $pool->total_hashrate];
        }

        return $return;
    }

    public function updateDailyRevenue (array $data)
    {
        foreach ($data as $assetId => $revenue)
        {
            $overview = Overview::where('id', '=', $assetId)->first();

            if ($overview && $overview->count() > 0)
            {
                $overview->daily_revenue = $revenue;
                $overview->save();
            }
        }

        return true;
    }

    public function updatePool (array $data)
    {
        foreach ($data as $poolId => $data)
        {
            $pool   = Pool::where ('id', '=', $poolId)->first();
            if ($pool && $pool->count() > 0)
            {
                $pool->used_power = $data['power'];
                $pool->total_hashrate = $data['hashrate'];
                $pool->save();
            }
        }

        return true;
    }

    public function asset()
    {
        return $this->belongsTo('App\Models\Asset\Asset' ,'asset_id');
    }
}
