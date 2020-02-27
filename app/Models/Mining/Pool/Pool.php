<?php

namespace App\Models\Mining\Pool;

use App\Models\Mining\Block;
use App\Models\Asset\Algo;
use App\Models\Asset\Asset;
use App\Models\Historical\HistoryAsset;
use App\Models\Mining\Miner\Miner;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asset\Units;
use App\Models\Mining\Contract;

class Pool extends Model
{

    public const SHA256 = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pool';

    public $timestamps = false;

    protected $fillable = [
        //'*',
        'id', 'name', 'asset_id', 'miners_count', 'total_hashrate', 'used_power'
    ];

    public function mine (Asset $asset, $poolData)
    {
        $block      = new Block();
        $block->generate (Miner::find(Miner::SLUSH), $asset);

        $asset->last_block_id   = $block->id;
        $asset->total_hashrate  = Units::toHashPerSecondLongFormat($poolData->btc->pool_scoring_hash_rate, $poolData->btc->hash_rate_unit); // @todo Change this when we will have multiple pools
        $asset->save();

        return $block;
    }

    static public function generateChart(?string $period = null, ?int $assetId = null) : array
    {
        if (empty ($period)) $period = '7d';
        $data   = $chartData = $return = $values = [];

        $seconds        = Units::periodToSeconds ($period);

        $now            = new \DateTime();
        $originPoint    = new \DateTime('-' . $seconds . ' seconds');
        $values         = [];


        $arcade = HistoryAsset::where([
            ['status', '>', '0'],
            ['created_at', '>=', $originPoint->getTimestamp()],
            ['asset_id', '=', $assetId]
        ])->get();

        foreach ($arcade as $item)
        {
            if ($item->asset->symbol == 'HASH') continue;
            $data[$item->asset->symbol][] =
                [
                    'x'         => (new \DateTime('@' . $item->created_at))->format(Units::DATETIME),
                    'y'         => $item->total_hashrate,
                ];

            $values[] = $item->hashrate;
        }

        foreach ($data as $symbol => $chartData)
        {
            $return[] =
                [
                    'id'            => $symbol,
                    'data'          => $chartData,
                ];
        }

        return ['chart' => $return, 'min' => count ($values) > 0 ? min ($values) : 0, 'max' => count ($values) > 0 ? max ($values) : 0];

    }

    static public function generatePowerConsumptionChart (?string $period = null, ?int $assetId = null) : array
    {
        if (empty ($period)) $period = '7d';
        $data   = $chartData = $values = [];

        $seconds        = Units::periodToSeconds ($period);

        $now            = $originalNow = new \DateTime();
        $parentOriginPoint = new \DateTime('-' . $seconds . ' seconds');

        $interval       = $seconds > 3600 * 24 ? 'PT1H' : 'PT15M';
        $min            = 9999999999999999999999;
        $max            = 0;

        $id             = null;
        $request        = null;
        $poolId         = null;
        $pools          = null;
        if (!empty ($assetId))
        {
            $algoId = Asset::findOrFail($assetId)->algo_id;
            $id = Algo::findOrFail ($algoId)->id;
            $poolId = $id;
        }

        if(!empty($poolId)){
            //todp: get method is needed?
            $pools = self::find ($poolId);//->get();
        }

        if($pools){
            foreach ($pools as $pool)
            {
                $assetData  =
                    [
                        'id'                => $pool->name,
                        'data'              => [],
                    ];

                $chartData      = [];
                $originPoint    = clone $parentOriginPoint;

                while ($now > $originPoint)
                {
                    $chartData[] =
                        [
                            'x'             => $originPoint->format(Units::DATETIME),
                            'y'             => $pool->used_power,
                        ];

                    $values[] = $pool->used_power;

                    if ($pool->used_power < $min) $min = $pool->used_power;
                    if ($pool->used_power > $max) $max = $pool->used_power;

                    $originPoint->add (new \DateInterval($interval));
                }

                $originPoint        = $parentOriginPoint;

                $assetData['data']  = $chartData;
                $data[] = $assetData;
            }
        }

        return ['chart' => $data, 'min' => $min / 2, 'max' => $max * 2];
    }

    public function algos()
    {
        return $this->hasMany(Algo::class);
    }

    public function miners()
    {
        return $this->hasMany(Miner::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class,'asset_id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }
}
