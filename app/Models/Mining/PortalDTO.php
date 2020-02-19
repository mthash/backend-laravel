<?php

namespace App\Models\Mining;

use App\Models\Asset\Asset;
use App\Models\User\AssetRepository as UserAssetRepository;
use Illuminate\Database\Eloquent\Model;
use App\Arcade;
use App\Models\Asset\Units;
use App\Models\User\User;
use App\Models\Historical\HistoryArcade;
use App\Models\Historical\HistoryAsset;

class PortalDTO extends Model
{
    private $user, $assets, $byCurrency = [];

    public function __construct(User $user)
    {
        $this->user         = $user;

        $assetsRelations    = UserAssetRepository::allVisible($user);

        foreach ($assetsRelations as $relation)
        {
            $asset      = Asset::findOrFail($relation->asset_id);

            $historical = HistoryArcade::changeToDay($this->user, $asset);

            $hashrate                   = $asset->getCurrentHashrate();
            $prettyHashrate             = Units::pretty($hashrate, 8);

            $singleUnit                 =
                [
                    'id'            => $asset->id,
                    'currency'      => $asset->symbol,
                    'algorithm'     => $asset->algo->name ?? 'N/A',
                    'value'         => $prettyHashrate['value'],
                    'unit'          => $prettyHashrate['unit'],
                    'shift'         => !empty ($historical->hashrate) ? Units::differencePercent($historical->hashrate, $hashrate) : 0,
                    'chart_data'    => $this->getChartData($asset),
                ];

            $this->assets[] = $singleUnit;
            $this->byCurrency[$asset->symbol] = $singleUnit;
        }
    }

    public function setData (array $investments)
    {
        foreach ($investments as $investment)
        {
            if ($investment->asset_id == 1) continue;

            $asset      = Asset::findOrFail($investment->asset_id);

            $historical = HistoryArcade::changeToDay($this->user, $asset);

            $hashrate                   = $asset->getCurrentHashrate();
            $prettyHashrate             = Units::pretty($hashrate, 8);

            $singleUnit                 =
                [
                    'id'            => $asset->id,
                    'currency'      => $asset->symbol,
                    'algorithm'     => $asset->algo->name ?? 'N/A',
                    'value'         => $prettyHashrate['value'],
                    'unit'          => $prettyHashrate['unit'],
                    'shift'         => !empty ($historical->hashrate) ? Units::differencePercent($historical->hashrate, $hashrate) : 0,
                    'chart_data'    => $this->getChartData($asset),
                ];

            $this->assets[] = $singleUnit;
            $this->byCurrency[$asset->symbol] = $singleUnit;
        }

    }

    private function getChartData(Asset $asset) : array
    {
        $data   = HistoryAsset::generateChart('1d', $asset->id);

        $lastValue  = $data[count($data)-1]['y'] ?? 0;
        $preLastValue  = $data[count($data)-2]['y'] ?? 0;

        switch ($lastValue <=> $preLastValue)
        {
            case 0: $trend  = 'neutral'; break;
            case 1: $trend  = 'positive'; break;
            case -1: $trend = 'negative'; break;
            default:
                $trend  = 'neutral';
        }

        return
            [
                'id'            => $asset->id,
                'trend'         => $trend,
                'data'          => $data,
            ];
    }

    public function getAsset(string $currency)
    {
        return $this->byCurrency[$currency] ?? [];
    }

    public function getAssets()
    {
        return $this->assets;
    }

}
