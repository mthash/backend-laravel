<?php

namespace App\Models\Mining;

use App\Models\Asset\Asset;
use App\Models\Asset\Units;
use App\Models\Historical\HistoryArcade;
use App\Models\User\Asset as UserAsset;
use App\Models\User\User;
use App\Models\User\Wallet;
use App\Models\User\WalletRepository;
use App\Models\Mining\Relayer;

class ContractDTO
{
    private $assets, $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function setData (array $investments)
    {
        foreach ($investments as $investment)
        {
            if ($investment->asset_id == 1) continue;

            $asset      = Asset::findOrFail($investment->asset_id);
            $historical = HistoryArcade::changeToDay(User::getCurrent(), $asset);

            $hashrate                   = Relayer::getUserCurrentHashrate(User::getCurrent(), $asset);
            $revenue                    = round (UserAsset::calculateRevenue(User::getCurrent(), $asset), 4);
            $prettyHashrate             = Units::pretty($hashrate);
            $balance                    = WalletRepository::byUserWithAsset(User::getCurrent(), $asset)->balance;


            $singleUnit    = [
                'id'                    => $asset->id,
                'currency'              => $asset->symbol,

                'revenue'               =>
                    [
                        'value'     => $revenue,
                        'unit'      => '/hr',
                        'shift'     => 0,
                        'usd'       => round ($revenue * $asset->price_usd, 2),
                    ],

                'hashrate'              =>
                    [
                        'value'     => $prettyHashrate['value'],
                        'unit'      => $prettyHashrate['unit'],
                        'shift'     => 0,
                    ],

                'mining'                =>
                    [
                        'value'     => number_format ($investment->hash_invested, 0, '.', ','),
                        'unit'      => 'HASH',
                        'shift'     => 0,
                        'usd'       => round ($investment->hash_invested * Asset::find(1)->price_usd, 2),
                    ],

                'balance'               =>
                    [
                        'value'     => $balance,
                        'unit'      => $asset->symbol,
                        'shift'     => 0,
                        'usd'       => round ($balance * $asset->price_usd, 2),
                    ],
            ];

            $singleUnit['revenue']['shift']     = !empty ($historical->revenue) ? Units::differencePercent($historical->revenue, $singleUnit['revenue']['value']) : 0;
            $singleUnit['hashrate']['shift']    = !empty ($historical->hashrate) ? Units::differencePercent($historical->hashrate, $hashrate) : 0;
            $singleUnit['mining']['shift']      = !empty ($historical->hash_invested) ? Units::differencePercent($historical->hash_invested, $singleUnit['mining']['value']) : 0;
            $singleUnit['balance']['shift']     = !empty ($historical->balance) ? Units::differencePercent($historical->balance, $singleUnit['balance']['value']) : 0;

            $this->assets[] = $singleUnit;
        }
    }

    public function getAssets()
    {
        return $this->assets;
    }
}
