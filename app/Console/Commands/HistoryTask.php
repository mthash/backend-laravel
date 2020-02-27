<?php

namespace App\Console\Commands;

use App\Models\Asset\Asset;
use App\Models\Historical\HistoryArcade;
use App\Models\Historical\HistoryWallet;
use App\Models\Historical\HistoryAsset;
use App\Models\Historical\HistoryDailyRevenue;
use App\Models\Mining\Contract;
use App\Models\Mining\Relayer;
use App\Models\Repositories\AssetRepository;
use App\Models\User\Wallet;
use DB;
use Illuminate\Console\Command;

/* CURRENT cron jobs running in Digitalocean
EXPLANATIONS:
Scheduled tasks are run via cron. There are 3 tasks at the moment:
— Coinmarketcap quotes updater (once in an hour)
— History watcher — running every 10 minutes — storing information about all balances and digits in the project, to show charts mainly
— Miner — running every minute, but there is an additional check for coin’s (token’s) block generation time — so, if new token could be generated only once in 10 minutes, miner will take this into account.

*       *       *       *       *       /usr/local/opt/php@7.2/bin/php /usr/local/var/www/mthash/cli.php mining start
0    *       *       *       *       /usr/local/opt/php@7.2/bin/php /usr/local/var/www/mthash/cli.php seeder quotes
*\/10    *       *       *       *       /usr/local/opt/php@7.2/bin/php /usr/local/var/www/mthash/cli.php history watch
*       *       *       *       *       /usr/local/opt/php@7.2/bin/php /usr/local/var/www/mthash/cli.php mining fluctuate
*/

class HistoryTask extends Command
{
    /**
     * The name and signature of the console command.
     * *\/10    *       *       *       *       /usr/local/opt/php@7.2/bin/php /usr/local/var/www/mthash/cli.php history watch
     *
     * @var string
     */
    protected $signature = 'hash:history:watch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'History watcher — running every 10 minutes — storing information about all balances and digits in the project, to show charts mainly';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //todo: add error handling and transactions
        $this->watchDailyRevenue();
        $this->watchArcade();
        $this->watchWallets();
        $this->watchAssets();
    }

    public function restartAction()
    {
        HistoryArcade::truncate();
        HistoryWallet::truncate();
        HistoryAsset::truncate();
        HistoryDailyRevenue::truncate();

        $this->watchAction();
    }

    public function watchAction()
    {
        $this->watchDailyRevenue();
        $this->watchArcade();
        $this->watchWallets();
        $this->watchAssets();
    }

    private function watchDailyRevenue()
    {
        //        $todayRevenue   = \Phalcon\Di::getDefault()->get('db')->query ('
        //            SELECT to_user_id as user_id, currency, SUM(amount) as amount, (SELECT price_usd FROM asset WHERE symbol = currency) as price_usd
        //            FROM transaction
        //            WHERE type_id = 2 AND from_user_id = -1 and (created_at >= ' . strtotime ('today 00:00:00') . ' AND created_at <= ' . strtotime ('today 23:59:59') . ')
        //            GROUP by currency
        //        ')->fetchAll (\PDO::FETCH_ASSOC);

        //todo: check the group by clause
        $todayRevenue = DB::select('
            SELECT to_user_id as user_id, currency, SUM(amount) as amount, (SELECT price_usd FROM asset WHERE symbol = currency) as price_usd
            FROM transaction
            WHERE type_id = 2 AND from_user_id = 1 and (created_at >= ' . strtotime('today 00:00:00') . ' AND created_at <= ' . strtotime('today 23:59:59') . ')
            GROUP by currency, to_user_id
        ');

        foreach ($todayRevenue as $item) {
            //            (new DailyRevenue())->createEntity(
            //                [
            //                    'user_id'               => $item['user_id'],
            //                    'asset_id'              => AssetRepository::bySymbol($item['currency'])->id,
            //                    'amount'                => $item['amount'],
            //                    'revenue'               => $item['price_usd'] * $item['amount'],
            //                ]
            //            );
            //            $dailyRevenue = new HistoryDailyRevenue;
            //            $dailyRevenue->user_id = $item['user_id'];
            //            $dailyRevenue->asset_id= AssetRepository::bySymbol($item['currency'])->id;
            //            $dailyRevenue->amount  = $item['amount'];
            //            $dailyRevenue->revenue = $item['price_usd'] * $item['amount'];

            HistoryDailyRevenue::create([
                'user_id'  => $item['user_id'],
                'asset_id' => AssetRepository::bySymbol($item['currency'])->id,
                'amount'   => $item['amount'],
                'revenue'  => $item['price_usd'] * $item['amount'],
            ]);

        }
    }

    private function watchArcade()
    {
        //        $contracts  = Relayer::find(
        //            [
        //                'status > 0',
        //                'group' => 'asset_id, user_id',
        //                'order' => 'id DESC',
        //            ]
        //        );

        //todo: fix the query
        $contracts = Relayer::where('status', '>', 0)
            ->groupBy('asset_id', 'user_id', 'id')
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($contracts as $contract) {
            //            (new Arcade())->createEntity(
            //                [
            //                    'revenue'           => round (\MtHash\Model\User\Asset::calculateRevenue($contract->user, $contract->asset), 4),
            //                    'user_id'           => $contract->user_id,
            //                    'asset_id'          => $contract->asset_id,
            //                    'hashrate'          => Relayer::getUserCurrentHashrate($contract->user, $contract->asset),
            //                    'balance'           => $contract->user->getWallet($contract->asset->symbol)->balance,
            //                    'hash_invested'     => ContractRepository::getUserInvestedHashByAsset($contract->user, $contract->asset),
            //                ]
            //            );

            HistoryArcade::create([
                'revenue'       => round(\App\Models\User\Asset::calculateRevenue($contract->user, $contract->asset), 4),
                'user_id'       => $contract->user_id,
                'asset_id'      => $contract->asset_id,
                'hashrate'      => Relayer::getUserCurrentHashrate($contract->user, $contract->asset),
                'balance'       => $contract->user->getWallet($contract->asset->symbol)->balance,
                'hash_invested' => Contract::getUserInvestedHashByAsset($contract->user, $contract->asset),
            ]);

        }
    }

    private function watchWallets(): void
    {
        //        $wallets = Wallet::find(
        //            [
        //                'status > 0 and user_id > 0',
        //            ]
        //        );

        $wallets = Wallet::where([
            ['status', '>', 0],
            ['user_id', '>', 0],
        ])
            ->get()
            //            ->toSql()
        ;
        //        dd($wallets);//
                foreach ($wallets as $wallet) {
        ////            dd($wallet->user_id, $wallet->balance);
        ////            (new Wallet())->createEntity(
        ////                [
        ////                    'wallet_id' => $wallet->id,
        ////                    'user_id'   => $wallet->user_id,
        ////                    'balance'   => $wallet->balance,
        ////                ]
        ////            );
                    HistoryWallet::create([
                        //'wallet_id' => $wallet->id, // this is Wallet's ID, I suppose it is not needed
                        'user_id'   => $wallet->user_id,
                        'balance'   => $wallet->balance,
                    ]);
                }
    }

    private function watchAssets()
    {
//        $assets = Asset::find(
//            [
//                'status > 0',
//            ]
//        );

        $assets = Asset::where('status', '>', 0)->get();
//dd(11, $assets);
        foreach ($assets as $asset) {
//            (new Asset())->createEntity(
//                [
//                    'asset_id'        => $asset->id,
//                    'tokens_invested' => $asset->hash_invested,
//                    'hashrate'        => $asset->getCurrentHashrate(),
//                    'total_hashrate'  => $asset->total_hashrate,
//                ]
//            );
            Asset::create([
                'asset_id'        => $asset->id,
                'tokens_invested' => $asset->hash_invested,
                'hashrate'        => $asset->getCurrentHashrate(),
                'total_hashrate'  => $asset->total_hashrate,
            ]);
        }
    }
}
