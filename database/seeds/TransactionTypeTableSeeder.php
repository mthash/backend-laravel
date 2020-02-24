<?php

use Illuminate\Database\Seeder;

class TransactionTypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('transaction_type')->delete();
        
        \DB::table('transaction_type')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'P2P',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Mining',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Fee',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Bonus',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Exchange',
            ),
        ));
        
        
    }
}