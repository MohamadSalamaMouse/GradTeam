<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use App\Notifications\EmailVerificationNotification;
use App\Traits\ImageProcessing;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ImageProcessing;

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

    public function updateProfile(Request $request)
    {
        $authUser = Auth::user();
        $id = $authUser->id;
    
        if ($authUser->id !== $id) {
            return response()->json([
                'code' => 0,
                'message' => 'Unauthorized. You can only update your own profile.',
            ], 401);
        }
    
        $user = User::findOrFail($id);
    
        $request->validate([
            'imageUrl' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', 
            'name' => 'nullable|string|max:255',
            'track' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:255',
            'githubUrl' => 'nullable|string|max:255',
            'facebookUrl' => 'nullable|string|max:255',
            'linkedinUrl' => 'nullable|string|max:255',
        ]);
    
        $fieldsToUpdate = [];
    
        if ($request->filled('name')) {
            $fieldsToUpdate['name'] = $request->name;
        }
    
        if ($request->filled('track')) {
            $fieldsToUpdate['track'] = $request->track;
        }
    
        if ($request->filled('bio')) {
            $fieldsToUpdate['bio'] = $request->bio;
        }
    
        if ($request->filled('githubUrl')) {
            $fieldsToUpdate['githubUrl'] = $request->githubUrl;
        }
    
        if ($request->filled('facebookUrl')) {
            $fieldsToUpdate['facebookUrl'] = $request->facebookUrl;
        }
    
        if ($request->filled('linkedinUrl')) {
            $fieldsToUpdate['linkedinUrl'] = $request->linkedinUrl;
        }
    
        if ($request->hasFile('imageUrl')) {
            $user->imageUrl ? $this->deleteImage($user->imageUrl) : '';
            $fieldsToUpdate['imageUrl'] = $this->saveImage($request->file('imageUrl'));
        }
    
        $user->fill($fieldsToUpdate);
        $user->save();
    
        return response()->json([
            'code' => 1,
            'message' => 'Profile updated successfully',
            'user' => $user,
        ], 200);
    }

}

