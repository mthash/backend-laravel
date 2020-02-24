<?php

namespace App\Console\Commands;

use App\Models\Asset\Algo;
use App\Models\Asset\Asset;
use App\Models\Asset\Units;
use App\Models\Mining\Block;
use App\Models\Mining\Distributor;
use App\Models\Mining\Pool\Pool;
use App\Models\Mining\Relayer;
use App\Models\Repositories\AssetRepository;
use Illuminate\Console\Command;
use App\SlushPool;
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

class MiningStartTask extends Command
{
    const MINERS_COUNT = 1;

    /**
     * The name and signature of the console command.
     *  /usr/local/opt/php@7.2/bin/php /usr/local/var/www/mthash/cli.php mining fluctuate
     * this cronjob runs every minute
     * @var string
     */
    protected $signature = 'hash:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'running every minute, but there is an additional check for coin’s (token’s) block generation time — so, if new token could be generated only once in 10 minutes, miner will take this into account.';

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
        try {


        /**
         * @var $assets Asset[]
         */
//        $assets = Asset::find(
//            [
//                'status > 0 and mineable = 1 and can_mine = 1',
//            ]
//        );

        //TODO: add error handling and transactions
        $poolData = SlushPool::getPoolStats();
//dd($poolData->btc->pool_scoring_hash_rate);
//dd(Units::toHashPerSecondLongFormat($poolData->btc->pool_scoring_hash_rate, $poolData->btc->hash_rate_unit));
        $assets = Asset::where([
            ['status', '>', 0],
            ['mineable', 1],
            ['can_mine', 1]
        ])->get();

        if ($assets->count() > 0) {
            foreach ($assets as $asset) {
                if ($asset->symbol != 'BTC') {
                    continue;
                }

                /**
                 * @var $lastBlock \Block
                 */
//                $lastBlock = Block::findFirst(
//                    [
//                        'asset_id = ?0 and status > 0',
//                        'bind'  => [$asset->id],
//                        'order' => 'id DESC',
//                    ]
//                );
//dd('a', $asset->id);
                /**
                 * @var $lastBlock \Block
                 */
                $lastBlock = Block::where(
                    [
                        ['asset_id', $asset->id],
                        ['status', '>', 0]
                    ]
                )
                ->orderBy('id', 'DESC')
                ->first();
//                dd($asset->id, $lastBlock);
                echo $asset->symbol ."\n";

                if ($lastBlock && $lastBlock->count() > 0) {
                    //dd(time(), $lastBlock->created_at->timestamp);

                    $difference = time() - $lastBlock->created_at->timestamp;
                    echo 'Date: ' . $lastBlock->created_at ."\n";
                    echo 'Difference: ' . $difference ."\n";
                    if ($difference < $asset->block_generation_time) {//getenv('IS_PRODUCTION') != 0 &&
                        continue;
                    }
                }

                /**
                 * @var Pool $pool
                 */
                $pool = Pool::find(Pool::SHA256);
                $pool->mine($asset, $poolData);

                //todo: get from contracts table
                Relayer::recalculateForAsset($asset);

                $distributor = new Distributor();
                $distributor->distributeRewards($asset);

            }
        }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function startAction()
    {
        /**
         * @var $assets Asset[]
         */
        $assets = Asset::find(
            [
                'status > 0 and mineable = 1 and can_mine = 1',
            ]
        );

        if ($assets->count() > 0) {
            foreach ($assets as $asset) {
                /**
                 * @var $lastBlock \MtHash\Model\Mining\Block
                 */
                $lastBlock = \MtHash\Model\Mining\Block::findFirst(
                    [
                        'asset_id = ?0 and status > 0',
                        'bind'  => [$asset->id],
                        'order' => 'id DESC',
                    ]
                );

                echo $asset->symbol . "\n";

                if ($lastBlock && $lastBlock->count() > 0) {
                    $difference = time() - $lastBlock->created_at;

                    if (getenv('IS_PRODUCTION') != 0 && $difference < $asset->block_generation_time) {
                        continue;
                    }
                }

                /**
                 * @var Pool $pool
                 */
                $pool = Pool::find(1);
                $pool->mine($asset);

                Relayer::recalculateForAsset($asset);

                $distributor = new Distributor();
                $distributor->distributeRewards($asset);

            }
        }
    }
    public function mineAction()
    {
        $asset = func_get_arg(0)[0] ?? Asset::DEFAULT_ASSET;
        $asset = AssetRepository::bySymbol($asset);

        /**
         * @var Pool $pool
         */
        $pool  = Pool::findFirst(1);
        $block = $pool->mine($asset);

        echo 'Block #' . $block->asset->symbol . '-' . $block->id . ' was generated <' . $block->hash . '>. Reward: ' . $block->reward . "\n";

        // @todo Move this to separate method
        Relayer::recalculateForAsset($asset);


        $distributor = new Distributor();
        $distributor->distributeRewards($asset);

    }
    public function fluctuateAction()
    {
        $pools = Pool::find();

        foreach ($pools as $pool) {
            $operation  = mt_rand(0, 100) > 50 ? 'plus' : 'minus';
            $percentage = mt_rand(1, 500) / 100; // from 0.1 to 5.0

            if ($operation == 'plus') {
                $pool->total_hashrate += $pool->total_hashrate * $percentage / 100;
            } else {
                $pool->total_hashrate -= $pool->total_hashrate * $percentage / 100;
            }

            $pool->save();

            // Updating assets
            $algos = Algo::find(['pool_id = ?0', 'bind' => [$pool->id]]);
            if ($algos) {
                foreach ($algos as $algo) {
                    $asset = Asset::findFirst(['algo_id = ?0', 'bind' => [$algo->id]]);
                    if ($asset) {
                        $asset->total_hashrate = $pool->total_hashrate;
                        $asset->save();
                    }
                }
            }
        }
    }
}
