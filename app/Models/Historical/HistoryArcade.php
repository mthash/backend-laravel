<?php

namespace App\Models\Historical;

use App\Models\Asset\Asset;
use App\Models\Asset\Units;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class HistoryArcade extends Model
{
    const SECONDS_IN_DAY = 3600;

    protected $dateFormat = 'U';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'history_arcade';

    protected $fillable = [
        //'*',
        'id',
        'user_id',
        'asset_id',
        'revenue',
        'hashrate',
        'hash_invested',
        'balance',
    ];

    public static function changeToDay(User $user, Asset $asset)
    {
        $record = self::where([
            ['status', '>', '0'],
            ['user_id', '=', $user->id],
            ['created_at', '>', time() - self::SECONDS_IN_DAY],
            ['asset_id', '=', $asset->id],
        ])->orderBy('id', 'ASC')->first();

        return $record;
    }

    static public function generateStaticChart(int $number, string $period)
    {

    }

    static public function generateChart(?string $period = null, ?int $assetId = null): array
    {
        if (empty ($period)) {
            $period = '7d';
        }
        $data = $return = $values = [];

        $seconds     = Units::periodToSeconds($period);
        $originPoint = new \DateTime('-' . $seconds . ' seconds');

        $arcade = HistoryAsset::where([
            ['status', '>', '0'],
            ['created_at', '>=', $originPoint->getTimestamp()],
            ['asset_id', '=', $assetId],
        ])->get();

        foreach ($arcade as $item) {
            $data[$item->asset->symbol][] =
                [
                    'x' => (new \DateTime('@' . $item->created_at))->format(Units::DATETIME),
                    'y' => $item->hash_invested,
                ];

            $values[] = $item->hash_invested;
        }

        foreach ($data as $symbol => $chartData) {
            $return[] = ['id' => $symbol, 'data' => $chartData];
        }

        if (count($values) < 1) {
            $values[] = 0;
        }

        return ['chart' => $return, 'min' => count($values) > 0 ? min($values) : 0, 'max' => count($values) > 0 ? max($values) : 0];
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function asset()
    {
        return $this->belongsTo('App\Models\Asset\Asset', 'asset_id');
    }
}
