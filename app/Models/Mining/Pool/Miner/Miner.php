<?php

namespace App\Models\Mining\Miner;

use Illuminate\Database\Eloquent\Model;
use App\Models\Asset\Algo;
use App\Models\Mining\Pool\Pool;
use App\Models\Mining\Block;

class Miner extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'miner';

    protected $fillable = [
        //'*',
        'id', 'pool_id', 'algo_id', 'max_hashrate'
    ];

    public function pool()
    {
        return $this->belongsTo(Pool::class,'pool_id');
    }

    public function algo()
    {
        return $this->belongsTo(Algo::class,'algo_id');
    }

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }
}
