<?php

namespace App\Http\Controllers\Block;

use App\Models\Mining\Block;
use App\Models\User\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BlockController extends Controller
{
    public function getRewardsWidget (Request $request, string $user = null)
    {
        //TODO: Make it work, what filters do we pass in??
        //return $this->request->getQuery()['filter'] ?? null;
        //dd(request()->get('filter'));
       // return response()->json(request()->get('filter'));
        $rewards = Block::getRewardsWidget(request()->get('filter'));
        return response()->json(['code'=> Controller::HTTP_OK, 'messeage' => 'null', 'body'=>$rewards]);
    }

    public function getMyRewardsWidget ()
    {
        $rewards    = Block::myRewardsWidget(User::getCurrent());
        return response()->json(['code'=> Controller::HTTP_OK, 'messeage' => 'null', 'body'=>$rewards]);
    }
}
