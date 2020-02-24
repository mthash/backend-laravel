<?php

use Illuminate\Database\Seeder;

class MiningPoolTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('mining_pool')->delete();

        \DB::table('mining_pool')->insert(array (
            0 =>
            array (
                'asset_id' => NULL,
                'id' => 1,
                'miners_count' => 1,
                'name' => 'SHA-256 Pool',
                'total_hashrate' => 10474744854343506,
                'used_power' => 9400000,
            ),
            1 =>
            array (
                'asset_id' => NULL,
                'id' => 2,
                'miners_count' => 1,
                'name' => 'ETHash Pool',
                'total_hashrate' => 88980542553,
                'used_power' => 4250000,
            ),
            2 =>
            array (
                'asset_id' => NULL,
                'id' => 3,
                'miners_count' => 1,
                'name' => 'Equihash Pool',
                'total_hashrate' => 48750648,
                'used_power' => 5350000,
            ),
            3 =>
            array (
                'asset_id' => NULL,
                'id' => 4,
                'miners_count' => 1,
                'name' => 'SCRYPT Pool',
                'total_hashrate' => 401804872033,
                'used_power' => 4400000,
            ),
            4 =>
                array (
                    'asset_id' => 3,
                    'id' => 5,
                    'miners_count' => 1,
                    'name' => 'Slush Pool',
                    'total_hashrate' => 1,
                    'used_power' => 1,
                ),
        ));


    }
}
