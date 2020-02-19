<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Firebase\JWT\JWT as JWTHandler;

//TODO: Make it work with Laravel
class Jwt extends Model
{
    const ALGO              = 'HS256';
    const EXP_KEYWORD       = 'exp';
    const EXP_SECONDS       = 3600 * 24 * 365;
    const HEADER            = 'AUTHORIZATION' ;//'HTTP_AUTHORIZATION';

    static public function sig() : string
    {
        $request = request();

        //TODO: Check out the cases if HTTP_ACCEPT returns more than */*
        $bestAccept = $_SERVER['HTTP_ACCEPT'];
        return sha1 (
            $request->ip() . $request->userAgent() . self::getBestLanguage() . $bestAccept
        );
    }

    static public function verifySig (?string $hash) : bool
    {
        //TODO: Check out the cases if HTTP_ACCEPT returns more than */*
        $bestAccept = $_SERVER['HTTP_ACCEPT'];
        //$bestLanguage = $request->getLocale();
        return sha1 (
            $request->ip() . $request->userAgent() . self::getBestLanguage() . $bestAccept
        ) === $hash;
    }

    static public function generate (array $data, ?string $secret = null)
    {
        if (is_null ($secret)) $secret = getenv('JWT_SECRET');

        $data[self::EXP_KEYWORD]    = time() + self::EXP_SECONDS;
        $data['for_testing']        = true;
        $data['sig']                = self::sig();

        return JWTHandler::encode ($data, $secret);
    }

    static public function fetch (string $jwt, ?string $secret = null, $algo = null)
    {
        $secret = is_null ($secret) ? getenv('JWT_SECRET') : $secret;
        $algo = is_null ($algo) ? self::ALGO : null;
        $algos[] = $algo;
//dd($jwt, $secret, $algos);
        $decoded    = (array) JWTHandler::decode ($jwt, $secret, $algos);

        // Turned off for demo purposes. Should be turned on later.
        //if ($decoded[self::EXP_KEYWORD] < time()) throw new \TokenException('Token is expired');
        //if (true !== self::verifySig($decoded['sig'])) throw new \TokenException('Incorrect signature');

        return $decoded;
    }

    static private function getBestLanguage(){
        $acceptLang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])?$_SERVER['HTTP_ACCEPT_LANGUAGE']:'';
        if(empty($acceptLang)){
            return '';
        }
        $prefLocales = array_reduce(
            explode(',', $acceptLang),
            function ($res, $el) {
                list($l, $q) = array_merge(explode(';q=', $el), [1]);
                $res[$l] = (float) $q;
                return $res;
            }, []);
        arsort($prefLocales);
    }
}
