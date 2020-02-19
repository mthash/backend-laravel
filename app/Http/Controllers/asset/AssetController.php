<?php

namespace App\Http\Controllers\Asset;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Asset\Asset;
use App\Models\Asset\AssetRepository;

class AssetController extends Controller
{
    public function getList()
    {
        $assets = Asset::all();
        return response()->json(['code'=> Controller::HTTP_OK, 'message' => 'null', 'body'=>$assets]);
    }

    public function getMineable()
    {
        return response()->json(['code'=> Controller::HTTP_OK, 'message' => 'null', 'body'=>AssetRepository::getMineable()]);
    }
}
