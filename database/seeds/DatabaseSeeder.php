<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(AlgoTableSeeder::class);
        $this->call(AssetTableSeeder::class);
        $this->call(BlockTableSeeder::class);
        $this->call(ContractTableSeeder::class);
        $this->call(HistoryArcadeTableSeeder::class);
        $this->call(HistoryAssetTableSeeder::class);
        $this->call(HistoryDailyRevenueTableSeeder::class);
        $this->call(MinerTableSeeder::class);
        $this->call(MiningPoolTableSeeder::class);
        $this->call(OverviewTableSeeder::class);
        $this->call(RelayerTableSeeder::class);
        $this->call(TransactionTableSeeder::class);
        $this->call(TransactionTypeTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(UserAssetTableSeeder::class);
        $this->call(WalletTableSeeder::class);
        $this->call(HistoryWalletTableSeeder::class);
    }
}
