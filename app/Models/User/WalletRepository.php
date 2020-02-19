<?php

namespace App\Models\User;

use App\Models\Asset\Units;
use App\Models\Asset\Asset;
use App\Models\Historical\HistoryWallet;

class WalletRepository
{
    /**
     * @param User $user
     * @return ResultsetInterface|Wallet[]
     */
    static public function byUser (User $user)
    {
        $wallets = Wallet::where([
            ['user_id', '=', $user->id],
            ['status', '>', '0']
        ])->get();

        return $wallets;
    }

    static public function byUserWithAsset (User $user, Asset $asset) : Wallet
    {
        return Wallet::where([
            ['user_id', '=', $user->id],
            ['asset_id', '=', $asset->id]
        ])->first();
    }

    static public function userHasCurrency (User $user, string $currency) : bool
    {
        $wallets    = self::byUser ($user);

        if ($wallets && $wallets->count() > 0)
        {
            $currencies = array_map (function ($v){ return $v['currency']; }, $wallets->toArray('currency'));
            return in_array ($currency, $currencies);
        }

        return false;
    }

    static public function getServiceWallet (string $service) : Wallet
    {
        return Wallet::where([
            ['status', '>', '0'],
            ['currency', '=', $service],
            ['user_id', '=', '-1']
        ])->first();
    }

    static public function getRegistrationCurrencies() : array
    {
        $currencies    = array_map (function ($v){ return $v['symbol']; }, Asset::find()->toArray());
        $currencies[]  = 'HASH';
        return $currencies;
    }

    static public function currencyByUser (User $user, string $currency) : Wallet
    {
        return Wallet::where([
            ['status', '>', '0'],
            ['currency', '=', $currency],
            ['user_id', '=', $user->id]
        ])->first();
    }

    static public function getHashBalanceWithChange (User $user)
    {
        $wallet = self::currencyByUser($user, 'HASH');
        $change = HistoryWallet::walletChangeToDay($user, $wallet);

        $hb =
            [
                'balance'           => $wallet->balance,
                'usd'               => round ($wallet->balance * $wallet->asset->price_usd, 2),
                'shift'             => Units::differencePercent($change->balance ?? 0, $wallet->balance),
                'unit'              => 'HASH',
            ];
        return $hb;
    }
}
