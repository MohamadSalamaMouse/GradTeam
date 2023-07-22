<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;


class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|max:255|unique:users',
            'password'=>'required|string|min:6',
        ]);
        if(User::where('email',$request->email)->exists()){
            return Response::json(
                [
                    'code'=>0,
                    'message'=>'Email already exists'
                ],401);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return Response::json(
            [
                'code'=>1,
                'access_token'=>$token,
                'user'=>$user

            ],201);
    }








    public function login(Request $request)
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
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Tokens revoked',
        ]);
    }
}
