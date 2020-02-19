<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;
use App\Models\Asset\Asset as AssetAsset;
use App\Models\Mining\Block;

class Asset extends Model
{
    protected $dateFormat = 'U';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_asset';

    static public function find (User $user, AssetAsset $asset) : Asset
    {
        $record = Asset::where([
            ['status', '>', '0'],
            ['user_id', '=', $user->id],
            ['asset_id', '=', $asset->id]
        ])->first();

        if (!$record)
        {
            $record = new Asset();
            $record->asset_id   = $asset->id;
            $record->user_id    = $user->id;
            $record->is_visible = 1;
            $record->save();
        }

        return $record;
    }

    public function show()
    {
        $this->is_visible   = 1;
        $this->save();
    }

    public function hide()
    {
        $this->is_visible   = 0;
        $this->save();
    }

    static public function calculateRevenue (User $user, AssetAsset $asset) : ?float
    {
        $results = \DB::select('
            SELECT SUM(`amount`) AS `revenue`
            FROM `transaction`
            WHERE `type_id` = 2 AND `from_user_id` = -1 and `created_at` > ' . strtotime ('-1 hour') . '
            AND `to_user_id` = ' . $user->id . ' AND `currency` = "' . $asset->symbol . '"
        ');
        return $results[0]->revenue;
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function asset()
    {
        return $this->belongsTo('App\Models\Asset\Asset' ,'asset_id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class,'block_id');
    }
}
