<?php
namespace App\Model\Asset;

use App\Models\User\Wallet;

interface Token
{
    public function transfer (Wallet $from, Wallet $to, int $amount) : string;

}
