<?php

use Illuminate\Database\Seeder;

class MinerTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('miner')->delete();

        \DB::table('miner')->insert(array (
            array (
                'algo_id' => 1,
                'id' => 1,
                'max_hashrate' => 15000000000,
                'pool_id' => 1,
            ),
            array (
                'algo_id' => 1,
                'id' => 2,
                'max_hashrate' => 10000000000000000000,
                'pool_id' => 5,
            ),
        ));


    }
}
