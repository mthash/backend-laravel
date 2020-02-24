<?php

use Illuminate\Database\Seeder;

class OverviewTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('overview')->delete();
        
        \DB::table('overview')->insert(array (
            0 => 
            array (
                'asset_id' => 2,
                'daily_revenue' => '35947.000',
                'id' => 1,
            ),
            1 => 
            array (
                'asset_id' => 3,
                'daily_revenue' => '52733.000',
                'id' => 2,
            ),
            2 => 
            array (
                'asset_id' => 4,
                'daily_revenue' => '7036.000',
                'id' => 3,
            ),
            3 => 
            array (
                'asset_id' => 5,
                'daily_revenue' => '52432.000',
                'id' => 4,
            ),
            4 => 
            array (
                'asset_id' => 6,
                'daily_revenue' => '20856.000',
                'id' => 5,
            ),
        ));
        
        
    }
}