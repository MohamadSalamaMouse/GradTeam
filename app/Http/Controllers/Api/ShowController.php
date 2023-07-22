<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    //
    public function ShowAll(){
         $users = User::all();
         $teams=Team::all();
        return response()->json([
            'users'=>$users,
            'teams'=>$teams
        ],200);
    }
}
