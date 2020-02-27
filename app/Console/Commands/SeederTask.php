<?php

namespace App\Console\Commands;

use App\Models\Mining\Block;
use App\Models\Mining\Contract;
use App\Models\Asset\Asset;
use App\Models\Asset\CMC;
use App\Models\Asset\Eth\Address;
use App\Models\Mining\HASHContract;
use App\Models\User\Wallet;
use App\Models\Mining\Pool\Pool;
use App\Models\Mining\Relayer;
use App\Models\Transaction\Transaction;
use App\Models\User\User;
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

class SeederTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hash:quotes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Coinmarketcap quotes updater (once in an hour)';

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
        /**
         * @var Asset[] $assets
         */
        $assets = Asset::where('mineable', 1)->get();
        $quotes = (new CMC($assets))->getResponse();

        //dd('quotes', $quotes);

        foreach ($assets as $asset) {
            $asset->price_usd = $quotes->data->{$asset->symbol}->quote->USD->price;
            $asset->save();

            echo $asset->symbol . ' new price is set to ' . $asset->price_usd . "\n";
        }
    }

    public function quotesAction()
    {
        /**
         * @var \MtHash\Model\Asset\Asset[] $assets
         */
        $assets = \MtHash\Model\Asset\Asset::find('mineable = 1');
        $quotes = (new \MtHash\Model\Asset\CMC($assets))->getResponse();

        print_r($quotes);

        foreach ($assets as $asset) {
            $asset->price_usd = $quotes->data->{$asset->symbol}->quote->USD->price;
            $asset->save();

            echo $asset->symbol . ' new price is set to ' . $asset->price_usd . "\n";
        }
    }

    private function truncate(\Phalcon\Mvc\Model $model): void
    {
        $this->getDI()->get('db')->query('TRUNCATE TABLE ' . $model->getSource() . '');
    }

    public function restartAction()
    {
        if (false == getenv('IS_PRODUCTION')) {
            Asset::truncate();
            \App\Models\User\Asset::truncate();
            Wallet::truncate();
            Block::truncate();
            Contract::truncate();
            Transaction::truncate();
            Relayer::truncate();
            Pool::truncate();

            (new HistoryTask())->restartAction();

            $this->poolsAction();
            $this->assetsAction();
            $this->usersAction();
            $this->walletsAction();

        } else {
            echo 'You can not restart on production';
        }
    }

    public function poolsAction()
    {
        $pools =
            [
                ['name' => 'SHA-256 Pool', 'miners_count' => 1, 'total_hashrate' => 194440000000000000, 'used_power' => 9400000],
                ['name' => 'ETHash Pool', 'miners_count' => 1, 'total_hashrate' => 2425000000000, 'used_power' => 4250000],
                ['name' => 'Equihash Pool', 'miners_count' => 1, 'total_hashrate' => 360000000, 'used_power' => 5350000],
                ['name' => 'SCRYPT Pool', 'miners_count' => 1, 'total_hashrate' => 4428000000000, 'used_power' => 4400000],
            ];

        foreach ($pools as $pool) {
            $p = new Pool();
            $p->name = $pool['name'];
            $p->miners_count = $pool['miners_count'];
            $p->total_hashrate = $pool['total_hashrate'];
            $p->used_power = $pool['used_power'];
            $p->save();
        }
    }

    public function assetsAction()
    {
        $assets =
            [
                'HASH' => ['name' => 'MtHash', 'price_usd' => 1, 'mineable' => 0, 'can_mine' => 0],
                'ETH'  => ['name' => 'Ethereum', 'price_usd' => 221, 'block_generation_time' => 600, 'block_reward_amount' => 3, 'algo_id' => 3],
                'BTC'  => ['name' => 'Bitcoin', 'price_usd' => 10432, 'block_generation_time' => 600, 'block_reward_amount' => 12.5, 'algo_id' => 1],
                'LTC'  => ['name' => 'Litecoin', 'price_usd' => 98, 'block_generation_time' => 600, 'block_reward_amount' => 12.5, 'algo_id' => 2],
                'BCH'  => ['name' => 'Bitcoin Cash', 'price_usd' => 315, 'block_generation_time' => 600, 'block_reward_amount' => 12.5, 'algo_id' => 1],
                //'ADA'           => ['name' => 'Cardano', 'price_usd' => 0.06, 'block_generation_time' => 600, 'block_reward_amount' => 10],
                //'TRX'           => ['name' => 'TRON', 'price_usd' => 0.02, 'block_generation_time' => 600, 'block_reward_amount' => 10],
                //'XMR'           => ['name' => 'Monero', 'price_usd' => 83, 'block_generation_time' => 600, 'block_reward_amount' => 10],
                //'DASH'          => ['name' => 'Dash', 'price_usd' => 116, 'block_generation_time' => 600, 'block_reward_amount' => 10],
                //'ETC'           => ['nam  e' => 'Ethereum Classic', 'price_usd' => 6.2, 'block_generation_time' => 600, 'block_reward_amount' => 10],
                'ZEC'  => ['name' => 'ZCash', 'price_usd' => 49.49, 'block_generation_time' => 600, 'block_reward_amount' => 10, 'algo_id' => 4],
            ];

        Asset::truncate();

        foreach ($assets as $symbol => $data) {
            $asset = new Asset();
            $asset->symbol   = $symbol;
            $asset->mineable = $asset->can_mine = $symbol == 'HASH' ? 0 : 1;

            switch ($symbol) {
                case 'BTC':
                case 'BCH':
                    $poolId = 1;
                    break;

                case 'ETH':
                    $poolId = 2;
                    break;
                case 'LTC':
                    $poolId = 4;
                    break;
                case 'ZEC':
                    $poolId = 3;
                    break;
                default:
                    $poolId = 1;
            }


            $asset->total_hashrate = Pool::find($poolId)->total_hashrate;
            $asset->last_block_id  = 0;
            $asset->save();
        }
    }

    public function walletsAction()
    {
        Wallet::truncate();

        foreach (Asset::all() as $asset) {
            $address             = Address::generate();
            $wallet              = new Wallet();
            $wallet->asset_id    = $asset->id;
            $wallet->user_id     = 1;
            $wallet->address     = $address['address'];
            $wallet->public_key  = $address['public'];
            $wallet->private_key = $address['private'];
            $wallet->currency    = $asset->symbol;
            $wallet->name        = $asset->symbol . ' Service Wallet';
            $wallet->balance     = 99999999999;
            $wallet->save();
        }

        // Creating wallets for all registered users
        foreach (User::where('status', '>', '0')->get() as $user) {
            $wallet = new Wallet();
            $wallet->createFor($user);
        }
    }

    public function usersAction()
    {
        try {
            $user = new User();
            $user->createDemo();
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }
    }

    // This is one-time call method to migrate from existing scheme of assets ownership to the new one.
    // It shouldn't be called and could be removed safely after successful demo
    public function migrate_assetsAction()
    {
        $assets = HASHContract::find(
            [
                'status > 0',
                'group' => ['user_id', 'asset_id'],
            ]
        );

        $this->truncate(new \App\Models\User\Asset());

        foreach ($assets as $relation) {
            (new \App\Models\User\Asset())->createEntity(
                [
                    'asset_id'   => $relation->asset_id,
                    'user_id'    => $relation->user_id,
                    'is_visible' => 1,
                ]
            );
        }
    }
}
