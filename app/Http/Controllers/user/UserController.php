<?php

namespace App\Http\Controllers\User;

use App\Exceptions\ValidationException;
use App\Models\User\User;
use App\Models\User\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required',
            'login' => 'required|email|unique:user',
            'password' => 'required|min:8'
        ]);

        if($v->fails()){
            throw new ValidationException($v->errors()->first());
        }

        $user = new User();
        $user->login = $request->get('login');
        $user->name = $request->get('name');
        $user->password = substr ($request->get('password'), 0, 4) == '$2y$' ? $request->get('password') : password_hash ($request->get('password'), PASSWORD_BCRYPT);
        $user->save();

        if($user){
            $wallet = new Wallet();
            $wallet->createFor($user);
        }

        return response()->json(['code'=> Controller::HTTP_CREATED, 'message' => 'null', 'body'=>$user]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
