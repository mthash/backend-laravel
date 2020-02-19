<?php

namespace App\Models\Historical;

use App\Models\Asset\Asset;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asset\Units;
use App\Models\Dashboard\Overview;

class HistoryDailyRevenue extends Model
{
    const SECONDS_IN_DAY = 3600;

    protected $dateFormat = 'U';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'history_daily_revenue';

    protected $fillable = [
        //'*',
        'id', 'user_id', 'asset_id', 'revenue'
    ];

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

    static public function generateChart(?string $period = null, ?int $assetId = null) : array
    {
        if (empty ($period)) $period = '7d';
        $data   = $chartData = $values = [];

        $seconds        = Units::periodToSeconds ($period);

        $now            = $originalNow = new \DateTime();
        $parentOriginPoint = new \DateTime('-' . $seconds . ' seconds');

        $interval       = $seconds > 3600 * 24 ? 'PT1H' : 'PT15M';
        $min            = 9999999999999999999999;
        $max            = 0;

        $overviews = Overview::where('asset_id', $assetId)->get();

        foreach ($overviews as $asset)
        {

            $assetData  =
                [
                    'id'                => $asset->asset->symbol,
                    'data'              => [],
                ];

            $chartData      = [];
            $originPoint    = clone $parentOriginPoint;

            while ($now > $originPoint)
            {
                $chartData[] =
                    [
                        'x'             => $originPoint->format(Units::DATETIME),
                        'y'             => $asset->daily_revenue,
                    ];

                $values[] = $asset->daily_revenue;

                if ($asset->daily_revenue < $min) $min = $asset->daily_revenue;
                if ($asset->daily_revenue > $max) $max = $asset->daily_revenue;

                $originPoint->add (new \DateInterval($interval));
            }

        $originPoint        = $parentOriginPoint;

        $assetData['data']  = $chartData;
        $data[] = $assetData;
        }

        return ['chart' => $data, 'min' => $min / 2, 'max' => $max * 2];
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
