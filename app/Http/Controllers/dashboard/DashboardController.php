<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Dashboard\Dashboard;

class DashboardController extends Controller
{
    public function getOverviewStatistics(Request $request)
    {
        $assetId = $request->get('asset_id');
        return response()->json(['code'=> Controller::HTTP_OK, 'message' => 'null', 'body'=>(new Dashboard())->getStatistics($assetId)]);
    }
}
