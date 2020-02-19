<?php

namespace App\Models\Historical;

use App\Models\Asset\Asset;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asset\Units;

class HistoryAsset extends Model
{
    const SECONDS_IN_DAY = 3600;

    protected $dateFormat = 'U';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'history_asset';

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
    static public function lastHashrateValue (int $assetId) : int
    {
        return (int) self::where([
            ['status', '>', '0'],
            ['asset_id', '=', $assetId],
        ])->orderBy('id', 'DESC')->first()->hashrate;
    }

    static public function generateChart(?string $period = null, ?int $assetId = null) : array
    {
        if (empty ($period)) $period = '7d';
        $data   = $return = $values = [];

        $seconds        = Units::periodToSeconds ($period);
        $originPoint    = new \DateTime('-' . $seconds . ' seconds');

        $data = HistoryAsset::where([
            ['status', '>', 0],
            ['asset_id', '=', $assetId],
            ['created_at', '>=', $originPoint->getTimestamp()]
        ])->get();


        foreach ($data as $point)
        {
            $return[] =
                [
                    'x'         => (new \DateTime('@' . $point->created_at))->format (Units::DATETIME),
                    'y'         => $point->hashrate,
                ];

            $values[] = $point->hashrate;
        }

        return ['chart' => $return, 'min' => count ($values) > 0 ? min ($values) : 0, 'max' => count ($values) > 0 ? max ($values) : 0];
    }

    public function asset()
    {
        return $this->belongsTo('App\Models\Asset\Asset' ,'asset_id');
    }
}
