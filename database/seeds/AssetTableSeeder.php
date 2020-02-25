<?php

use Illuminate\Database\Seeder;

class AssetTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('asset')->delete();

        \DB::table('asset')->insert(array(
            0 =>
                array(
                    'algo_id'               => 1,
                    'block_generation_time' => null,
                    'block_reward_amount'   => null,
                    'can_mine'              => 0,
                    'cmc_id'                => null,
                    'created_at'            => 1570111813,
                    'deleted_at'            => null,
                    'hash_invested'         => 0,
                    'id'                    => 1,
                    'last_block_id'         => 0,
                    'logo_url'              => null,
                    'mineable'              => 0,
                    'name'                  => 'MtHash',
                    'price_usd'             => '1.0000',
                    'shares'                => null,
                    'status'                => 1,
                    'symbol'                => 'HASH',
                    'total_hashrate'        => 194440000000000000,
                    'updated_at'            => null,
                ),
            1 =>
                array(
                    'algo_id'               => 3,
                    'block_generation_time' => 600,
                    'block_reward_amount'   => 3.0,
                    'can_mine'              => 1,
                    'cmc_id'                => null,
                    'created_at'            => 1570111813,
                    'deleted_at'            => null,
                    'hash_invested'         => 0,
                    'id'                    => 2,
                    'last_block_id'         => 4138,
                    'logo_url'              => null,
                    'mineable'              => 1,
                    'name'                  => 'Ethereum',
                    'price_usd'             => '174.0302',
                    'shares'                => null,
                    'status'                => 1,
                    'symbol'                => 'ETH',
                    'total_hashrate'        => 88980542553,
                    'updated_at'            => 1580832901,
                ),
            2 =>
                array(
                    'algo_id'               => 1,
                    'block_generation_time' => 600,
                    'block_reward_amount'   => 12.5,
                    'can_mine'              => 1,
                    'cmc_id'                => null,
                    'created_at'            => 1570111813,
                    'deleted_at'            => null,
                    'hash_invested'         => 0,
                    'id'                    => 3,
                    'last_block_id'         => 914,
                    'logo_url'              => null,
                    'mineable'              => 1,
                    'name'                  => 'Bitcoin',
                    'price_usd'             => '8273.5614',
                    'shares'                => null,
                    'status'                => 1,
                    'symbol'                => 'BTC',
                    'total_hashrate'        => 10474744854343506,
                    'updated_at'            => 1580832901,
                ),
            3 =>
                array(
                    'algo_id'               => 2,
                    'block_generation_time' => 600,
                    'block_reward_amount'   => 12.5,
                    'can_mine'              => 1,
                    'cmc_id'                => null,
                    'created_at'            => 1570111813,
                    'deleted_at'            => null,
                    'hash_invested'         => 0,
                    'id'                    => 4,
                    'last_block_id'         => 915,
                    'logo_url'              => null,
                    'mineable'              => 1,
                    'name'                  => 'Litecoin',
                    'price_usd'             => '54.6776',
                    'shares'                => null,
                    'status'                => 1,
                    'symbol'                => 'LTC',
                    'total_hashrate'        => 401804872033,
                    'updated_at'            => 1580832901,
                ),
            4 =>
                array(
                    'algo_id'               => 1,
                    'block_generation_time' => 600,
                    'block_reward_amount'   => 12.5,
                    'can_mine'              => 1,
                    'cmc_id'                => null,
                    'created_at'            => 1570111813,
                    'deleted_at'            => null,
                    'hash_invested'         => 0,
                    'id'                    => 5,
                    'last_block_id'         => 916,
                    'logo_url'              => null,
                    'mineable'              => 1,
                    'name'                  => 'Bitcoin Cash',
                    'price_usd'             => '230.8030',
                    'shares'                => null,
                    'status'                => 1,
                    'symbol'                => 'BCH',
                    'total_hashrate'        => 192890579020635872,
                    'updated_at'            => 1571749202,
                ),
            5 =>
                array(
                    'algo_id'               => 4,
                    'block_generation_time' => 600,
                    'block_reward_amount'   => 10.0,
                    'can_mine'              => 1,
                    'cmc_id'                => null,
                    'created_at'            => 1570111813,
                    'deleted_at'            => null,
                    'hash_invested'         => 0,
                    'id'                    => 6,
                    'last_block_id'         => 917,
                    'logo_url'              => null,
                    'mineable'              => 1,
                    'name'                  => 'ZCash',
                    'price_usd'             => '36.8675',
                    'shares'                => null,
                    'status'                => 1,
                    'symbol'                => 'ZEC',
                    'total_hashrate'        => 48750648,
                    'updated_at'            => 1580832901,
                ),
        ));


    }
}
