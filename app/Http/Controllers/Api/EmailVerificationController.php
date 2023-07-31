<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Api\EmailVerificationRequest;
use App\Notifications\EmailVerificationNotification;
use Otp;

class EmailVerificationController extends Controller
{
    private $otp;
    
    public function __construct()
    {
        $this->otp = new Otp;
    }

    public function emailVerification(EmailVerificationRequest $request)
    {
        $otp2 = $this->otp->validate($request->email, $request->otp);

        if (!$otp2->status) {
            return response()->json([
                'code'=>0,
                'message' => 'Invalid OTP'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'code'=>0,
                'message' => 'User not found'
            ], 401);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'code'=>0,
                'message' => 'Email address has already been verified'
            ], 401);
        }

        $user->update(['email_verified_at' => now()]);

        // Generate a new access token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'code'=>1,
            'message' => 'Email address has been verified successfully',
            'access_token' => $token,
            'user'=>$user
        ], 200);
    }
}