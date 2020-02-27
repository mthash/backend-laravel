<?php

namespace App\Models\Asset;

use App\Models\Mining\HASHContract;

class AssetRepository
{
    /**
     * @param Asset $asset
     * @return HASHContract[]
     */
    static public function getInvestorsContracts(Asset $asset)
    {
        return HASHContract::where(
            [
                ['status', '>', 0],
                ['asset_id', $asset->id],
            ])
            ->groupBy('user_id', 'id')
            ->get();
    }


    static public function getInvestorsInvestment(Asset $asset)
    {
        $result = \DB::select("
            SELECT user_id, SUM(amount) as hash_invested
            FROM contract
            WHERE status > '0' and asset_id = " . $asset->id . "
            GROUP by user_id
            HAVING SUM(amount) > 0
        ");

        return $result;
    }

    /**
     * @param string $symbol
     * @return \MtHash\Model\AbstractEntity|Asset|\Phalcon\Mvc\Model
     * @throws \BusinessLogicException
     * @throws \ReflectionException
     */
    public static function bySymbol(string $symbol)
    {
        return Asset::where([
            ['status', '>', '0'],
            ['symbol', '=', $symbol],
        ])->first();
    }

    static public function getMineable()
    {
        return Asset::where([
            ['status', '>', '0'],
            ['mineable', '=', '1'],
            ['can_mine', '=', '1'],
        ])->get();
    }

    static public function allSymbols(): array
    {
        return Asset::where([
            ['status', '>', '0'],
        ])->get('symbol')->toArray();
    }
}
