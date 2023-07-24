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


 public function emailVerification(EmailVerificationRequest $request){

     $otp2 = $this->otp->validate($request->email, $request->otp);
     if(!$otp2->status){
        return response()->json(['error' => $otp2],401);
     }

      $user = User::where('email',$request->email)->first();
      $user->update(['email_verified_at' => now()]);
      
      $success['success'] = true;
      return response()->json($success,200);

  }
}
