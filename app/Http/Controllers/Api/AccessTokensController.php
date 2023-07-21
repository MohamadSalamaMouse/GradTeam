<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class AccessTokensController extends Controller
{
    //
    public function store(Request $request)
    {
        $request->validate([
            'email'=>'required|email|max:255',
            'password'=>'required|string|min:6',
            'device_name'=>'string|max:255',
        ]);
        $user = User::where('email',$request->email)->first();
        if($user && Hash::check($request->password,$user->password)){
            $device_name=$request->post('device_name',$request->UserAgent());
            $token= $user->createToken($device_name);
            return Response::json(
                [    'code'=>1,
                    'access_token'=>$token->plainTextToken,
                    'user'=>$user

                ]
                ,201);
        }

        return Response::json(
            [
                'code'=>0,
                'message'=>'Invalid Credentials'
            ],401);

    }
}
