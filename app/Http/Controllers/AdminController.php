<?php

namespace App\Http\Controllers;

use App\Models\Dashboard\Overview;
use App\Models\User\User;
use Illuminate\Http\Request;
use App\Console\Commands\SeederTask;

class AdminController extends Controller
{
    private function checkAccess() : void
    {
       if (1 !== User::getCurrent()->is_admin) throw new \Exception('Access denied');
    }

    public function getRestart()
    {
        $this->checkAccess();

        $handler    = new SeederTask();
        $handler->restartAction();

        return response()->json(['code'=> Controller::HTTP_OK, 'message' => 'null', 'body'=>Controller::HTTP_OK]);
    }

    public function getOverview()
    {
        $this->checkAccess();
        $overview = new Overview();
        return response()->json(['code'=> Controller::HTTP_OK, 'message' => 'null', 'body'=>$overview->getStaticData()]);
    }

    public function postOverview(Request $request)
    {
        $this->checkAccess();
        $dailyRevenue = $request->get('daily_revenue');

        $power = $request->get('power');

        if (isset ($dailyRevenue))
        {
            (new Overview())->updateDailyRevenue($dailyRevenue);
        }

        if (isset ($power))
        {
            (new Overview())->updatePool($power);
        }

        return response()->json(['code'=> Controller::HTTP_OK, 'message' => 'null', 'body'=>['ok']]);
    }
}
