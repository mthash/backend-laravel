<?php

namespace App\Models\Mining;

use App\Model\Mining\BlockDTO;
use App\Models\Mining\Miner\Miner;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asset\Asset;
use App\Models\User\User;
use App\Models\Mining\Widget\MyRewardsDTO;
use App\Models\Mining\Pool\Pool;
use App\Models\Mining\Contract;
use Illuminate\Support\Facades\DB;
use App\Models\Mining\Relayer;
use App\Models\Transaction\Transaction;


/**
 * Class Block
 * @package App\Models\Mining
 */
class Block extends Model
{
    /**
     * @var string
     */
    protected $dateFormat = 'U';

    //    public $timestamps = false;
    /**
     *
     */
    const CREATED_AT = 'created_at';
    /**
     *
     */
    const UPDATED_AT = null;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'block';

    protected $fillable = [
        'asset_id', 'miner_id', 'pool_id', 'hash', 'reward', 'status'
    ];

    /**
     * @param Miner $miner
     * @param Asset $asset
     * @return $this
     */
    public function generate(Miner $miner, Asset $asset): self
    {
        $this->create(
            [
                'asset_id' => $asset->id,
                'miner_id' => $miner->id,
                'pool_id'  => $miner->pool_id,
                'hash'     => $this->getBlockHash(),
                'reward'   => $this->getBlockReward($asset),
            ]
        );

        return $this;
    }

    /**
     * @return string
     */
    private function getBlockHash()
    {
        return hash('SHA256', microtime(true));
    }

    /**
     * @param Asset $asset
     * @return mixed
     */
    private function getBlockReward(Asset $asset)
    {
        return $asset->block_reward_amount;
    }

    /**
     * @param array|null $filter
     * @return array
     * @throws \App\Exceptions\BusinessLogicException
     */
    public static function getRewardsWidget(?array $filter = null): array
    {
        //TODO:: Replace with Laravel
        $request = "status > '0'";
        if (empty ($filter)) {
            $filter = [];
        }

        $prepared = new RewardFilter($request, $filter);
//        dd($prepared->getRequest());
//        $blocks = self::where(
//            [
//                $prepared->getRequest(),
//                'bind'  => $prepared->getBind(),
//                'limit' => 20,
//                'order' => 'id DESC',
//            ]
//        );
        //status > 0 and created_at > 1581799773
        $blocks =  DB::select('
            SELECT * from block where ' . $prepared->getRequest() . ' order by id DESC limit 20
        ');

//        $blocks = Block::where('status', '>', 0)->whereRaw($prepared->getRequest())->get();

        $response = [];
        foreach ($blocks as $block) {
            $response[] = (new BlockDTO($block))->fetch();
        }

        return $response;
    }

    /**
     * @param User $user
     * @return array
     */
    static public function myRewardsWidget(User $user): array
    {
        return (new MyRewardsDTO($user))->fetch();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function asset()
    {
        return $this->belongsTo('App\Models\Asset\Asset', 'asset_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function miner()
    {
        return $this->belongsTo(Miner::class, 'miner_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pool()
    {
        return $this->belongsTo(Pool::class, 'pool_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relayers()
    {
        return $this->hasMany(Relayer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
