<?php

namespace App\Http\Controllers\Mining;

use App\Models\Historical\HistoryArcade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Mining\Pool\Pool;
use App\Models\Asset\Algo;
use App\Models\Historical\HistoryDailyRevenue;

class ChartController extends Controller
{
    private function possibleChartTypes() : array
    {
        return ['pools', 'algorithms', 'tokens', 'power', 'daily_revenue'];
    }

    private function possiblePeriodLiterals() : array
    {
        return ['1h', '3h', '1d', '7d', '1m', 'all'];
    }

    public function getChart (Request $request, ?string $type = null)
    {
        $period     = $request->get('period') ?? null;
        $assetId    = $request->get('asset_id') ?? null;

        if ($period == 'all') $period = null;

        $v = Validator::make($request->all(), [
            'type' => 'nullable|in:'.implode(',', $this->possibleChartTypes()),
            'asset_id' => 'nullable|numeric',
            'period' => 'alpha_num|in:'.implode(',', $this->possiblePeriodLiterals())
        ]);

        if($v->fails()){
            throw new ValidationException($v->errors()->first());
        }

      /*  $this->validateInput(
            [
                'type'          => $type,
                'asset_id'      => $assetId,
                'period'        => $period,
            ],
            [
                'type'          => ['optional', ['in', $this->possibleChartTypes()]],
                'asset_id'      => ['optional', 'numeric'],
                'period'        => ['alphaNum', ['in', $this->possiblePeriodLiterals()]]
            ]
        );*/

        $data   = [];

        switch ($type)
        {
            case 'pools':
                $data   = Pool::generateChart($period, $assetId);
            break;

            case 'algorithms':
                $data   = Algo::generateChart($period, $assetId);
            break;

            case 'tokens':
                $data   = HistoryArcade::generateChart($period, $assetId);
            break;

            case 'power':
                $data   = Pool::generatePowerConsumptionChart($period, $assetId);
            break;

            case 'daily_revenue':
                $data   = HistoryDailyRevenue::generateChart($period, $assetId);
            break;

            default:
                $data['pools']          = Pool::generateChart($period);
                $data['algorithms']     = Algo::generateChart($period);
                $data['tokens']         = HistoryArcade::generateChart($period, $assetId);
                $data['power']          = Pool::generatePowerConsumptionChart($period);
                $data['daily_revenue']  = HistoryDailyRevenue::generateChart($period, $assetId);
        }
        return response()->json(['code'=> Controller::HTTP_OK, 'message' => 'null', 'body'=>$data]);
    }
}
