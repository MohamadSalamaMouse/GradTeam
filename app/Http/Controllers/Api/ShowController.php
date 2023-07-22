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
    public function viewTeamMembers($id){
     $team = Team::find($id);
     $members=$team->members;

     return response()->json([
         'code'=>1,
         'team'=>$team,



     ],201);
    }

    public function StoreTeam(Request $request){
        $request->validate([
            'name'=>'required|string|max:255',
            'description'=>'nullable|string',
        ]);
        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        return response()->json([
            'code'=>1,
            'message'=>'Team created successfully',

        ],201);
    }
   public function deleteTeam($id){
       $team = Team::find($id);
       $team->delete();
       return response()->json([
           'code'=>1,
           'message'=>'Team deleted successfully'
       ],201);
   }
//   public function CreateMember(Request $request){
//        User::create([
//            'name' => $request->name,
//            'email' => $request->email,
//            'team_id' => $request->team_id,
//            'track' => $request->track,
//            'bio' => $request->bio,
//            'imageUrl' => $request->imageUrl,
//            'githubUrl' => $request->githubUrl,
//            'facebookUrl' => $request->facebookUrl,
//            'linkedinUrl' => $request->linkedinUrl,
//
//        ]);
//        return response()->json([
//            'code'=>1,
//            'message'=>'Member created successfully',
//            'request'=>$request->team_id
//        ],201);
//
//
//   }

}
