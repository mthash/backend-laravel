<?php

namespace App\Models\Mining\Pool;

interface MiningPool
{
    public function generateBlock() : string;
    public function canGenerateBlock() : bool;
    public function transferTokens();
}
