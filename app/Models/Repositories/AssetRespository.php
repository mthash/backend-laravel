<?php

namespace App\Models\Repositories;

use App\Models\Asset\Asset;
use App\Models\Mining\HASHContract;

//TODO: Do we need this class? There is an original in Models/Asset/AssetRepository
/**
 * Class AssetRespository
 *
 * @package \App\Repositories
 */
class AssetRepository
{
    /**
     * @param Asset $asset
     * @return ResultsetInterface|HASHContract[]
     */
    static public function getInvestorsContracts (Asset $asset)
    {
        return HASHContract::where (
            [
                ['status', '>', 0],
                ['asset_id', $asset->id]
            ]
        )
            ->groupBy('user_id', 'id')
            ->get();
    }


    static public function getInvestorsInvestment(Asset $asset)
    {
        $result = \DB::select('
            SELECT `user_id`, SUM(`amount`) as `hash_invested`
            FROM `contract`
            WHERE `status` > 0 and `asset_id` = ' . $asset->id . '
            GROUP by `user_id`
            HAVING SUM(`amount`) > 0
        ');

        return $result;
    }

    /**
     * @param string $symbol
     * @return \MtHash\Model\AbstractEntity|Asset|\Phalcon\Mvc\Model
     * @throws \BusinessLogicException
     * @throws \ReflectionException
     */
    public static function bySymbol (string $symbol)
    {
        //User::where("email","test@example.com"->firstOrFail()
        return Asset::where('status', '>', '0')->where('symbol', $symbol)->firstOrFail();
    }

    static public function getMineable()
    {
        return Asset::where([
            ['status', '>', '0'],
            ['mineable', '=', '1'],
            ['can_mine', '=', '1'],
        ])->get();
    }

    static public function allSymbols() : array
    {
        return Asset::where([
            ['status', '>', '0'],
        ])->get('symbol')->toArray();
    }
}
