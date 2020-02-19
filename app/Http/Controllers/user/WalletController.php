<?php

namespace App\Http\Controllers\User;

use App\Models\User\User;
use App\Models\User\Wallet;
use App\Models\User\WalletRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function getList()
    {
        $user = User::getCurrent();
        $wallets = WalletRepository::byUser($user);

        // Temporary
        if ($wallets->count() == 0)
        {
            //TODO: Test creating new wallet
            (new Wallet())->createFor($user);
            $wallets    = $wallets = WalletRepository::byUser($user);
        }

        return response()->json(['code'=> 200, 'message' => 'null', 'body'=>$wallets]);
    }
}
