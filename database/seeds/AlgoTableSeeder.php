<?php

use Illuminate\Database\Seeder;

class AlgoTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('algo')->delete();

        \DB::table('algo')->insert(array(
            0 =>
                array(
                    'id'      => 1,
                    'name'    => 'SHA-256',
                    'pool_id' => 1,
                    'status'  => 1,
                ),
            1 =>
                array(
                    'id'      => 2,
                    'name'    => 'SCRYPT',
                    'pool_id' => 4,
                    'status'  => 1,
                ),
            2 =>
                array(
                    'id'      => 3,
                    'name'    => 'ETHASH',
                    'pool_id' => 2,
                    'status'  => 1,
                ),
            3 =>
                array(
                    'id'      => 4,
                    'name'    => 'EQUIHASH',
                    'pool_id' => 3,
                    'status'  => 1,
                ),
            4 =>
                array(
                    'id'      => 5,
                    'name'    => 'Ouroboros',
                    'pool_id' => 3,
                    'status'  => -1,
                ),
        ));


    }
}
