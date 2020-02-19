<?php

namespace App\Http\Controllers\Mining;

use App\Models\Mining\ContractDTO;
use App\Models\Asset\AssetRepository;
use App\Models\User\Asset as UserAsset;
use App\Models\Mining\Contract;
use App\Models\Mining\PortalDTO;
use App\Models\User\User;
use App\Models\User\WalletRepository;
use App\Http\Controllers\Controller;

class WidgetController extends Controller
{
    public function getArcadeBlock()
    {
        $response = new ContractDTO(User::getCurrent());

        $response->setData(Contract::getUserInvestmentsPerAsset(User::getCurrent()));

        return response()->json(['code'=> Controller::HTTP_OK, 'message' => 'null', 'body'=>$response->getAssets()]);
    }

    public function getPortalBlock()
    {
        $response = new PortalDTO(User::getCurrent());
        return response()->json(['code'=> Controller::HTTP_OK, 'messeage' => 'null', 'body'=>$response->getAssets()]);
    }

    public function getHashBalance()
    {
        $response = WalletRepository::getHashBalanceWithChange(User::getCurrent());
        return response()->json(['code'=> Controller::HTTP_OK, 'messeage' => 'null', 'body'=>$response]);
    }

    public function postCreateAsset (string $asset)
    {
        $userAsset = UserAsset::find(User::getCurrent(), AssetRepository::bySymbol($asset));

        $userAsset->show();

        $response   = new PortalDTO(User::getCurrent());

        return response()->json(['code'=> Controller::HTTP_CREATED, 'message' => 'null', 'body'=>$response->getAsset($userAsset->asset->symbol)]);
    }

    public function deleteAsset (string $asset)
    {
        //TODO: Make it work with Laravel
        /*$asset      = \App\Model\User\AssetRepository::find ($this->getUser(), AssetRepository::bySymbol($asset));
        $asset->makeInvisible();

        // Withdraw
        $contract   = new HASHContract();
        $operation  = $contract->withdraw(
            $this->getUser()->getWallet(),
            Asset::failFindFirst(['symbol = ?0', 'bind' => [$asset]])
        );

        $response   = new PortalDTO($this->getUser());

        $this->webResponse($response->getAsset($asset->asset->symbol));*/
    }
}
