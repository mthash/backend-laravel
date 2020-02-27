<?php

namespace App\Models\User;

use App\Exceptions\BusinessLogicException;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\User\Wallet;
use App\Models\Historical\HistoryDailyRevenue;
use App\Exceptions\TokenException;
use App\Models\Mining\Contract;
use App\Models\Mining\Relayer;

class User extends Authenticatable
{
    use Notifiable;

    const   DEMO_USER_ID = 2;

    protected $dateFormat = 'U';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'login',
        'password',
        'is_demo',
        'is_admin',
        'tag',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public static function getCurrent()
    {
        $token = request()->header(Jwt::HEADER);
//dd($token,  request()->header());
        if (empty ($token)) {
            dd('empty token', token);
            throw new TokenException('Authorization Token is empty');
        }

        $tokenData = Jwt::fetch($token);

        return self::findOrFail($tokenData['id']);
    }

    //TODO: Adjust to Laravel
    //    public function getWallet (?string $symbol = null) : Wallet
    //    {
    //        if (empty ($symbol)) $symbol = 'HASH';
    //        return Wallet::failFindFirst(['status > 0 and currency = ?0 and user_id = ?1', 'bind' => [$symbol, $this->id]]);
    //    }

    public function createDemo(?string $tag = null): User
    {
        if (is_null($tag)) {
            $tag = time();
        }

        $demoUser = new User();

        $demoUser->login    = 'demo-' . $tag . '@mthash.com';
        $demoUser->name     = 'Demo ' . $tag . '-User';
        $demoUser->password = password_hash(12345678, PASSWORD_BCRYPT);
        $demoUser->is_demo  = 1;
        $demoUser->tag      = $tag;

        $demoUser->save();

        return $demoUser;
    }

    public function getWallet(?string $symbol = null): Wallet
    {
        if (empty ($symbol)) {
            $symbol = 'HASH';
        }

        return Wallet::where([
            ['status', '>', '0'],
            ['currency', '=', $symbol],
            ['user_id', '=', $this->id],
        ])->first();
    }

    public static function failFindFirst($parameters = null)
    {
        $entity = self::first($parameters);
        if (!$entity) {
            $message = 'Such ' . (new \ReflectionClass(static::class))->getShortName() . ' does not exists';
            if (getenv('APP_ENV') != 'production') {
                $message .= print_r($parameters, 1);
            }
            throw new BusinessLogicException($message);
        }

        return $entity;
    }

    public function wallets()
    {
        return $this->hasMany('App\Models\User\Wallet');
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function relayers()
    {
        return $this->hasMany(Relayer::class);
    }

    public function userAssets()
    {
        return $this->hasMany('App\Models\User\Asset');
    }

    public function historyArcades()
    {
        return $this->hasMany('App\Models\Historical\HistoryArcade');
    }

    public function historyDailyRevenues()
    {
        return $this->hasMany(HistoryDailyRevenue::class);
    }

    public function historyWallets()
    {
        return $this->hasMany('App\Models\Historical\HistoryWallet');
    }
}
