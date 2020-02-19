<?php

namespace App\Models\Mining;

use App\Models\Asset\Asset;
use App\Models\Transaction\Transaction;
use App\Models\User\Wallet;

interface ContractInterface
{
    public function canDeposit (Wallet $wallet, Asset $asset, float $hashToken) : bool;
    public function canWithdraw (Wallet $wallet, Asset $asset, float $hashToken) : bool;

    public function deposit (Wallet $wallet, Asset $asset, float $hashToken) : Contract;
    public function withdraw (Wallet $wallet, Asset $asset, float $hashToken) : Contract;

    public function getAllocatedTokens (Asset $asset);
    public function calculateUserHashrate (Asset $asset);
    public function getUserAllocatedTokens (Asset $asset);

}
