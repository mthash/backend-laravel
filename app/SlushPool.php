<?php

namespace App;

use App\Exceptions\BusinessLogicException;
use GuzzleHttp\Client as Client;

class SlushPool
{
    protected static $client;

    private static function getClient()
    {
        if(!self::$client){
            self::$client = new Client([
                'base_uri' => 'https://slushpool.com/',
                'headers' => ['SlushPool-Auth-Token' => env('SLUSHPOOL_API_KEY')]
            ]);
        }
        return self::$client;
    }

    public static function request($method, $url){

        try{
            $client = self::getClient();

            $response = $client->request($method, $url);

            return \GuzzleHttp\json_decode($response->getBody()->getContents());

        }catch (\Exception $e){
            throw new BusinessLogicException('Slush exception: '. $e->getMessage());
        }
    }

    public static function getPoolStats()
    {
        return self::request('GET', 'stats/json/btc/');
    }

    public static function getUserProfile()
    {
        return self::request('GET', 'accounts/profile/json/btc');
    }
}
