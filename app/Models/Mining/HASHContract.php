<?php

namespace App\Models\Mining;

use App\Models\Asset\Asset;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\BusinessLogicException;
use App\Models\User\User;
use App\Models\Mining\Relayer;
use App\Models\User\Wallet;
use Illuminate\Support\Facades\Artisan;

class HASHContract extends Model implements ContractInterface
{
    public $id, $wallet_id, $user_id, $asset_id, $tokens_count, $hashrate, $block_id;

    protected $dateFormat = 'U';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contract';

    protected $fillable = [
        'id', 'wallet_id', 'user_id', 'asset_id', 'tokens_count', 'hashrate', 'block_id'
    ];

//    public function initialize()
//    {
//        $this->setSource ('contract');
//        $this->belongsTo ('user_id', User::class, 'id', ['alias' => 'user']);
//        $this->belongsTo ('asset_id', Asset::class, 'id', ['alias' => 'asset']);
//        $this->belongsTo ('wallet_id', Wallet::class, 'id', ['alias' => 'wallet']);
//        $this->belongsTo ('block_id', Block::class, 'id', ['alias' => 'block']);
//    }

    public function canDeposit(Wallet $wallet, Asset $asset, float $hashToken): bool
    {
        if ($wallet->user_id != User::getCurrent()->id) throw new BusinessLogicException('Authorized user is not owner of the wallet');
        if ($wallet->currency != 'HASH') throw new BusinessLogicException('Wallet must in HASH');
        if ($wallet->balance < $hashToken) throw new BusinessLogicException('Insufficient HASH on Wallet');
        if ($hashToken <= 0) throw new BusinessLogicException('Amount can not be less or equals zero');

        return true;
    }

    public function canWithdraw(Wallet $wallet, Asset $asset, float $hashToken): bool
    {
        if ($wallet->user_id != User::getCurrent()->id) throw new BusinessLogicException('Authorized user is not owner of the wallet');
        if ($wallet->currency != 'HASH') throw new BusinessLogicException('Wallet must in HASH');
        if ($hashToken <= 0) throw new BusinessLogicException('Amount can not be less or equals zero');

        $limit  = $this->getUserAllocatedTokens($asset);

        if ($hashToken > $limit) throw new BusinessLogicException('Insufficient HASH on contract. Available: ' . $limit);

        return true;
    }

    public function deposit(Wallet $wallet, Asset $asset, float $hashToken): Contract
    {
        if (!$this->canDeposit($wallet, $asset, $hashToken)) throw new \BusinessLogicException('Can not deposit ' . $hashToken . ' HASH');

        if ($wallet->withdraw($hashToken))
        {
            // Freezing this tokens on SC
            $contract = new Contract();
            $contract->user_id = User::getCurrent()->id;
            $contract->wallet_id = $wallet->id;
            $contract->asset_id = $asset->id;
            $contract->amount = $hashToken;
            $contract->block_id = $asset->last_block_id > 0 ? $asset->last_block_id : 1;
            $contract->save();

            $asset->hash_invested+= $hashToken;
            $asset->save();

            $contract->hashrate = $this->calculateUserHashrate($asset);
            $contract->save();

            Relayer::recalculateForAsset($asset);

            //(new \HistoryTask())->watchAction();
            Artisan::call('hash:history:watch');

            return $contract;
        }

        throw new BusinessLogicException('Can not withdraw tokens from wallet');
    }

    public function withdraw(Wallet $wallet, Asset $asset, ?float $hashToken = null): Contract
    {
        if (is_null ($hashToken)) $hashToken    = Contract::getUserInvestmentsPerAsset($wallet->user, $asset);
        if (!$this->canWithdraw($wallet, $asset, $hashToken)) throw new BusinessLogicException('Can not withdraw ' . $hashToken . ' HASH');

        if ($wallet->deposit($hashToken))
        {
            // Freezing this tokens on SC
            $contract = new Contract();
            $contract->user_id = User::getCurrent()->id;
            $contract->wallet_id = $wallet->id;
            $contract->asset_id = $asset->id;
            $contract->amount = -1 * $hashToken;
            $contract->block_id = $asset->last_block_id > 0 ? $asset->last_block_id : 1;
            $contract->save();

            $contract->hashrate = $this->calculateUserHashrate($asset);
            $contract->save();

            $asset->hash_invested-= $hashToken;
            $asset->save();

            Relayer::recalculateForAsset($asset);

            //(new \HistoryTask())->watchAction();
            Artisan::call('hash:history:watch');

            return $this;
        }

        throw new BusinessLogicException('Can not deposit tokens to wallet');
    }

    public function getAllocatedTokens (Asset $asset)
    {

        $contracts = Contract::where([
            ['status', '>', '0'],
            ['asset_id', '=', $asset->id]
        ])->get(['amount']);

        return (int) $contracts->sum('amount');
    }

    public function predictUserHashrate (Asset $asset, int $userTokens, bool $isDeposit = true)
    {
        $operation  = true === $isDeposit ? $asset->hash_invested + $userTokens : $asset->hash_invested - $userTokens;
        return $userTokens * $asset->total_hashrate / $operation;
    }

    public function calculateUserHashrate (Asset $asset) : int
    {
        $userTokens = $this->getUserAllocatedTokens($asset);

        if ($userTokens < 0.00000001) return 0;

        return $userTokens * $asset->total_hashrate / $asset->hash_invested;
    }

    public function getUserAllocatedTokens (Asset $asset) : int
    {
        $contracts = Contract::where([
            ['status', '>', '0'],
            ['asset_id', '=', $asset->id],
            ['user_id', '=', User::getCurrent()->id]
        ])->get(['amount']);

        return (int) $contracts->sum('amount');
    }
}
