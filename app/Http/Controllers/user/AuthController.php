<?php

namespace App\Http\Controllers\User;

use App\Models\User\User;
use Illuminate\Http\Request;
use App\Exceptions\BusinessLogicException;
use App\Http\Controllers\Controller as Controller;
use App\Models\User\Jwt;

class AuthController extends Controller
{
    public function getListDemoUsers()
    {
//        try {
            $demoUsers  = User::where('status', '>', 0)->where('is_demo', 1)->get(['id', 'name', 'login', 'tag']);
            return response()->json(['code'=> 200, 'message' => 'null', 'body'=>$demoUsers]);

//        } catch (Exception $e) {
//            return response()->json(['code'=> 200, 'body'=> []]);
//        }
        return $this->webResponse($demoUsers);

    }

    public function postDemoLogin(Request $request)
    {
        $demoUser = User::where('login','demo@mthash.com') -> first();

        if(!$demoUser){
            $demoUser =  (new User())->createDemo();
        }

        $tokenData  = $demoUser->toArray(['id', 'name', 'login', 'created_at', 'status', 'is_demo', 'is_admin']);
        $tokenData['iat']   = time();

        $encodedTokenData   = Jwt::generate($tokenData);

        return response()->json(['code'=> 200, 'message' => 'null', 'body'=>$encodedTokenData]);

    }

    public function postDemoSpecifiedLogin (string $tag)
    {
        $demoUser = User::where('tag', $tag)->first();

        if (!$demoUser)
        {
            $demoUser =  (new User())->createDemo($tag);
        }

        $tokenData  = $demoUser->only(['id', 'name', 'login', 'created_at', 'status', 'tag', 'is_admin', 'is_demo']);
        $tokenData['iat']   = time();

        $encodedTokenData   = Jwt::generate($tokenData);

        return response()->json(['code'=> 200, 'message' => 'null', 'body'=>$encodedTokenData]);

    }

    public function postLogin(Request $request)
    {
        $requestData = json_decode($request->getContent());
        $user = User::where('status', '>', 0)
        ->where('login', $requestData->{'login'})->first();
//        return response()->json(['code'=> 404, 'message' => '', 'body'=>null, json_decode($request->getContent())->{'login'}]);
        if(!$user){
            throw new BusinessLogicException('User not found: ' . $request->all());
        }

        if (!password_verify($requestData->{'password'}, $user->password)) {
            throw new BusinessLogicException('Incorrect password');
        }

        $tokenData  = $user->only (['id', 'name', 'login', 'created_at', 'status', 'is_admin', 'is_demo']);
        $tokenData['iat']   = time();

        $encodedTokenData   = Jwt::generate($tokenData);

        return response()->json(['code'=> 200, 'message' => 'null', 'body'=>$encodedTokenData]);
    }
}
