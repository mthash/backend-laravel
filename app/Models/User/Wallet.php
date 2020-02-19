<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use App\Models\Asset\Eth\Address;
use App\Exceptions\BusinessLogicException;
use App\Models\User\User;
use App\Models\Asset\Asset;
use App\Models\Historical\HistoryWallet;
use App\Models\Asset\Units;
use App\Models\Mining\Contract;

class Wallet extends Model
{
    protected $dateFormat = 'U';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wallet';

    //todo: add fields
    protected $fillable = [
        //'*'
        'id', 'asset_id', 'address', 'public_key', 'private_key', 'user_id', 'currency', 'balance', 'name'
    ];

    public function createFor (User $user) : bool
    {
        foreach (Asset::all() as $asset)
        {
            if(empty($asset->symbol)) continue;
            if (WalletRepository::userHasCurrency($user, $asset->symbol)) continue;

            $address    = Address::generate();
            $balance    = $asset->symbol == 'HASH' ? 10000 : 0;

            $walet = new Wallet();
            $walet->asset_id = $asset->id;
            $walet->currency = $asset->symbol;
            $walet->user_id = $user->id;
            $walet->name = 'My ' .  $asset->symbol . ' Wallet';
            $walet->address = $address['address'];
            $walet->public_key = $address['public'];
            $walet->private_key = $address['private'];
            $walet->balance = $balance;

            $walet->save();
        }
        return true;
    }

    public function canSendTo (Wallet $wallet, float $amount) : bool
    {
        if ($this->currency != $wallet->currency) throw new BusinessLogicException('Can not send tokens to other token currency');
        if ($this->balance < $amount) throw new BusinessLogicException('Insufficient funds');
        if ($amount <= 0.00) throw new BusinessLogicException('You can not send zero or negative amount of tokens (' . $amount . ')');
        return true;
    }

    public function deposit (float $amount) : Wallet
    {
        $this->balance+= $amount;
        $this->save();
        return $this;
    }

    public function withdraw (float $amount) : Wallet
    {
        $this->balance -= $amount;
        $this->save();
        return $this;
    }

    public function asset()
    {
        return $this->belongsTo('App\Models\Asset\Asset' ,'asset_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function historyWallets()
    {
        return $this->hasMany('App\HistoryWallet');
    }
}
