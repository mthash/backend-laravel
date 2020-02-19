<?php

namespace App;

use App\Exceptions\BusinessLogicException;
use App\Models\Asset\Asset;
use App\Models\User\WalletRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User\Wallet;
use App\Models\Mining\Block;

class Transaction extends Model
{
    const   NEW         = 1;
    const   FAILED      = 2;
    const   PROCESSED   = 3;

    protected $dateFormat = 'U';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaction';

    public function freeDeposit (Asset $asset, Wallet $to, float $amount, ?int $typeId = null, ?int $blockId = null, ?float $percent = null) : Transaction
    {
        $from   = WalletRepository::getServiceWallet($asset->symbol);

        if (empty ($typeId)) $typeId = TransactionType::BONUS;

        try{
            DB::beginTransaction();

            $transaction = new Transaction();
            $transaction->wallet_from_id = $from->id;
            $transaction->wallet_to_id = $to->id;
            $transaction->from_user_id = -1;
            $transaction->to_user_id = $to->user_id;
            $transaction->amount = $amount;
            $transaction->currency = $from->currency;
            $transaction->condition = self::NEW;
            $transaction->type_id = $typeId;
            $transaction->percent = $percent;
            $transaction->block_id = $blockId;

            $from->canSendTo ($from, $amount);

            $from->withdraw ($amount);
            $to->deposit($amount);

            $this->condition    = self::PROCESSED;
            $this->save();

            DB::commit();

        }catch(\Exception $e){
            DB::rollBack();
            throw new BusinessLogicException($e->getMessage());
        }

        return $this;
    }

    public function exchange (Wallet $from, Asset $asset, float $amount)
    {
        if ($asset->symbol == $from->currency) throw new BusinessLogicException('You can not exchange same currency');

        $serviceWalletTo    = WalletRepository::getServiceWallet($from->asset->symbol);
        $serviceWalletFrom  = WalletRepository::getServiceWallet($asset->symbol);
        $to                 = WalletRepository::byUserWithAsset($from->user, $asset);

        $exchangeRate   = Asset::calculateExchangeRate(
            $from->asset, $asset, $amount
        );
        $exchangedAmount    = $amount * $exchangeRate;

        $from->canSendTo($serviceWalletTo, $amount);
        $serviceWalletFrom->canSendTo($to, $exchangedAmount);

        try{
            DB::beginTransaction();

            $fromUserToService = new Transaction();
            $fromUserToService->wallet_from_id = $from->id;
            $fromUserToService->wallet_to_id = $serviceWalletTo->id;
            $fromUserToService->amount = $amount;
            $fromUserToService->currency = $from->currency;
            $fromUserToService->condition = self::NEW;
            $fromUserToService->type_id = TransactionType::EXCHANGE;

            $from->withdraw($amount);

            $fromServiceToUser = new Transaction();
            $fromServiceToUser->wallet_from_id = $serviceWalletFrom->id;
            $fromServiceToUser->wallet_to_id = $to->id;
            $fromServiceToUser->amount = $exchangedAmount;
            $fromServiceToUser->currency = $serviceWalletFrom->currency;
            $fromServiceToUser->condition = self::NEW;
            $fromServiceToUser->type_id = TransactionType::EXCHANGE;

            $to->deposit ($exchangedAmount);

            DB::commit();

        }catch(\Exception $e){
            DB::rollBack();
            throw new BusinessLogicException($e->getMessage());
        }

        $fromUserToService->condition   = self::PROCESSED;
        $fromServiceToUser->condition   = self::PROCESSED;

        $fromServiceToUser->save();
        $fromUserToService->save();

        return true;
    }

    public function block()
    {
        return $this->belongsTo(Block::class,'block_id');
    }

    public function type()
    {
        return $this->belongsTo('App\TransactionType' ,'type_id');
    }
}
