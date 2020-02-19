<?php

namespace App\Models\Asset;

use App\Models\Historical\HistoryAsset;
use Illuminate\Database\Eloquent\Model;
use App\Models\Mining\Miner\Miner;
use App\Models\Mining\Pool\Pool;

class Algo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'algo';

    protected $fillable = [
        //'*',
        'id', 'name', 'pool_id'
    ];

    static public function generateChart(?string $period = null, ?int $assetId = null) : array
    {
        if (empty ($period)) $period = '7d';

        $return         = $data = $chartData = $values = [];
        $seconds        = Units::periodToSeconds ($period);

        $originPoint    = new \DateTime('-' . $seconds . ' seconds');

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
                    'y'         => $item->hashrate,
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

    public function pool()
    {
        return $this->belongsTo(Pool::class,'pool_id');
    }

    public function assets()
    {
        return $this->hasMany('App\Models\Asset\Asset');
    }

    public function miners()
    {
        return $this->hasMany(Miner::class);
    }
}
