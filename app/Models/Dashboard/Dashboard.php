<?php

namespace App\Models\Dashboard;

use App\Models\Asset\Algo;
use App\Models\Asset\Asset;
use App\Models\Mining\Pool\Pool;

class Dashboard
{
    private function getPoolCount(?int $assetId) : int
    {
        return !empty ($assetId) ? 1 : Pool::all()->count();
    }

    private function getAlgorithmsCount(?int $assetId = null) : int
    {
        return !empty ($assetId) ? 1 : Algo::where('status', '>', '0')->count();
    }

    private function getTokensCount(?int $assetId = null) : int
    {
        if(!empty($assetId)){
            return Asset::where('id', '=', $assetId)->sum('hash_invested');
        }

        return Asset::all()->sum('hash_invested');
    }

    private function getPower(?int $assetId = null) : array
    {
        if (!empty ($assetId)) $asset  = Asset::findOrFail($assetId);

        $power  = empty ($asset) ? \DB::select('SELECT SUM(used_power) as power FROM pool') : $asset->algo->pool->used_power;

        if(is_array($power)){
            $power = $power[0]->power;
        }
        return
            [
                'value'         => $power / 1000000,
                'unit'          => 'MW',
            ];
    }

    private function getDailyRevenue(?int $assetId = null) : array
    {
        /*$request    = !empty ($assetId) ? ' AND currency = "' . Asset::failFindFirst($assetId)->symbol . '"': '';
        $todayRevenue   = \Phalcon\Di::getDefault()->get('db')->query ('
            SELECT currency, SUM(amount) as amount, (SELECT price_usd FROM asset WHERE symbol = currency) as price_usd
            FROM transaction
            WHERE type_id = 2 AND from_user_id = -1 and (created_at >= ' . strtotime ('today 00:00:00') . ' AND created_at <= ' . strtotime ('today 23:59:59') . ') ' . $request . '
            GROUP by currency
        ')->fetchAll (\PDO::FETCH_ASSOC);

        $revenue    = 0;
        foreach ($todayRevenue as $item)
        {
            $revenue+= $item['amount'] * $item['price_usd'];
        }*/

        if (!empty ($assetId))
        {
            $revenue = Overview::where('asset_id', '=', $assetId)->sum('daily_revenue');
        }
        else
        {
            $revenue = Overview::all()->sum('daily_revenue');
        }


        $isMillion  = $revenue / 1000000 > 1;

        return
            [
                'raw'           => $revenue,
                'value'         => $isMillion ? round ($revenue / 1000000, 2) : round ($revenue / 1000, 2),
                'unit'          => $isMillion ? 'M' : 'K',
            ];
    }


    public function getStatistics(?int $assetId = null) : array
    {
        return
            [
                'pools'                 => $this->getPoolCount($assetId),
                'algorithms'            => $this->getAlgorithmsCount($assetId),
                'tokens'                => $this->getTokensCount($assetId),
                'power'                 => $this->getPower($assetId),
                'daily_revenue'         => $this->getDailyRevenue($assetId),
            ];
    }
}
