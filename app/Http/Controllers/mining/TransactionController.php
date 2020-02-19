<?php

namespace App\Http\Controllers\Mining;

use App\Models\Asset\Asset;
use App\Exceptions\BusinessLogicException;
use App\Exceptions\ValidationException;
use App\Models\Asset\Units;
use App\Models\Mining\PortalDTO;
use App\Models\Asset\AssetRepository;
use App\Models\User\User;
use App\Models\User\Wallet;
use App\Models\User\WalletRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Mining\HASHContract;
use Illuminate\Support\Facades\Validator;


class TransactionController extends Controller
{
    public function postDeposit (Request $request, string $asset)
    {
        $v = Validator::make($request->all(), [
            'amount' => 'required|numeric'
        ]);

        if($v->fails()){
            throw new ValidationException($v->errors()->first());
        }

        $contract   = new HASHContract();

        $assetObj = Asset::where('symbol', '=', $asset)->first();

        $operation  = $contract->deposit(
            User::getCurrent()->getWallet(),
            $assetObj,
            $request->get('amount')
        );

        $response   = new PortalDTO(User::getCurrent());
        return response()->json(['code'=> Controller::HTTP_CREATED, 'message' => 'null', 'body'=>$response->getAsset($asset)]);
    }

    public function getMaxValues (string $asset)
    {
        $asset = AssetRepository::bySymbol($asset);
        $deposit =  WalletRepository::currencyByUser(User::getCurrent(), 'HASH')->balance;
        $withdraw = (new HASHContract())->getUserAllocatedTokens($asset);

        $max = [
            'deposit'       => $deposit,
            'withdraw'      => $withdraw
        ];

        return response()->json(['code'=> Controller::HTTP_OK, 'message' => 'null', 'body'=>$max]);
    }

    public function getHashratePrediction (Request $request, string $asset)
    {

        $v = Validator::make($request->all(), [
            'amount' => 'required|numeric'
        ]);

        if($v->fails()){
            throw new ValidationException($v->errors()->first());
        }

        $amount = $request->get('amount');

        $asset = AssetRepository::bySymbol($asset);

        $contract = new HASHContract();

        $tokensDep  = $contract->getUserAllocatedTokens($asset) + $amount;
        $tokensWth  = $contract->getUserAllocatedTokens($asset) - $amount;

        $prediction =  [
            'deposit'       => Units::pretty($contract->predictUserHashrate($asset, $tokensDep, true)),
            'withdraw'      => Units::pretty($contract->predictUserHashrate($asset, $tokensWth, false)),
        ];

        return response()->json(['code'=> Controller::HTTP_OK, 'message' => 'null', 'body'=>$prediction]);
    }

    public function postWithdraw (Request $request, string $asset)
    {
        $v = Validator::make($request->all(), [
            'amount' => 'required|numeric'
        ]);

        if($v->fails()){
            throw new ValidationException($v->errors()->first());
        }

        $contract = new HASHContract();

        $assetObj = Asset::where('symbol', '=', $asset)->first();

        $operation  = $contract->withdraw(
            User::getCurrent()->getWallet(),
            $assetObj,
            $request->get('amount')
        );

        $response   = new PortalDTO(User::getCurrent());
        return response()->json(['code'=> Controller::HTTP_OK, 'message' => 'null', 'body'=>$response->getAsset($asset)]);
    }
}
