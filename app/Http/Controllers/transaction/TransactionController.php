<?php

namespace App\Http\Controllers\Transaction;

use App\Models\Asset\Asset;
use App\Models\User\WalletRepository;
use App\Transaction;
use App\Models\User\User;
use App\Models\User\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\BusinessLogicException;

class TransactionController extends Controller
{
    public function postFreeDeposit(Request $request)
    {
        $v = Validator::make($request->all(), [
            'asset_id' => 'required',
            'amount' => 'required|numeric'
        ]);

        if($v->fails()){
            throw new ValidationException($v->errors()->first());
        }

        //TODO: Is it ok to throw 404, or should we throw businessLogicException instead?
        $asset = Asset::findOrFail($request->get('asset_id'));

        $transaction = new Transaction();
        $response = $transaction->freeDeposit(
            $asset, WalletRepository::byUserWithAsset(User::getCurrent(), $asset), $request->get('amount')
        );

        return response()->json(['code'=> Controller::HTTP_OK, 'message' => 'null', 'body'=>$response]);
    }

    public function postExchange(Request $request, string $currency)
    {
        $wallet = Wallet::findOrFail($request->get('wallet_id'));

        if ($wallet->user_id != User::getCurrent()->id) throw new BusinessLogicException('You are not the owner of the wallet');

        $transaction    = new Transaction();
        $asset = Asset::where('symbol', '=', $currency)->first();

        $response = $transaction->exchange (
            $wallet, $asset, $request->get('amount')
        );

        return response()->json(['code'=> Controller::HTTP_OK, 'message' => 'null', 'body'=>$response]);

    }
}
