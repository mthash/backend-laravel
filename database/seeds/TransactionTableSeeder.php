<?php

use Illuminate\Database\Seeder;

class TransactionTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('transaction')->delete();
        
        
        
    }
}