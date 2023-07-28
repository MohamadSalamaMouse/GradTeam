<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Join;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ShowController extends Controller
{
    public function ShowAll()
    {
        $users = User::all();
        $teams = Team::all();
        $count=[];
        foreach ($teams as $team){
           $count[$team->id] = $team->members->count();
         }

        return response()->json([
            'users' => $users,
            'teams' => $teams,
            'count'=>$count

        ], 200);
    }

    public function viewTeamMembers()
    {
        $id=request()->team_id;
        $team = Team::find($id);
        $members = $team->members;

        return response()->json([
            'code' => 1,
            'team' => $team,



        ], 201);
    }

    public function findTeam(Request $request)
    {
        $team_id = $request->team_id;
        $team = Team::find($team_id);
        return response()->json([
            'team' => $team
        ], 201);
    }

    public function StoreTeam(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        $user_id = $request->user()->id;

        $user = User::find($user_id);

        if ($user->isLeader == 0) {

            $team = Team::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);
            $team_id = Team::latest()->first();

            $user->team_id = $team_id->id;
            $user->isLeader = 1;
            $team->Num_of_Members=1;
            $user->save();
            $team->save();
            return response()->json([
                'code' => 1,
                'message' => 'Team created successfully',

            ], 201);
        }

        return response()->json([
            'code' => 0,
            'message' => 'You can not create team',
        ], 401);


    }

    public function deleteTeam(Request $request)
    {
          $team_id=$request->team_id;

        if( $request->user()->isLeader == 1 && $request->user()->team_id == $team_id) {
            $team = Team::find($team_id);
            $members = $team->members;
            foreach ($members as $member) {
                if($member->isLeader == 1){
                    $member->isLeader = 0;
                }
                $member->team_id=null;
                $member->save();
            }

            $team->delete();
            return response()->json([
                'code' => 1,
                'message' => 'Team deleted successfully'
            ], 201);
        } else{
            return response()->json([
                'code' => 0,
                'message' => 'You can not delete team'
            ], 401);
        }

    }

    public function JoinTeam(Request $request)
    {
        $user_id = $request->user()->id;
        $team_id = $request->team_id;
        $existingJoin = Join::where('user_id', $user_id)
            ->where('team_id', $team_id)
            ->first();

        if ($existingJoin) {
            return response()->json([
                'code' => 0,
                'message' => 'You have already requested to join this team',
            ], 409); // HTTP 409 Conflict
        }
        $join = new Join();
        $join->user_id = $user_id;
        $join->team_id = $team_id;
        $join->join = true;
        if ($join->save()) {
            return response()->json([
                'code' => 1,
                'message' => 'Team requested successfully',
            ], 201);
        }

    }

    public function requests(Request $request)
    {
        $team_id = $request->team_id;
        $IsLeader = $request->user()->isLeader;
        if ($IsLeader == 1 && $request->user()->team_id == $team_id) {
            $existingJoin = Join::where('team_id', $team_id)->get();
            $data = [];
            foreach ($existingJoin as $join) {
                $data[]=$join->user;
            }


            return response()->json([
                'code' => 1,
                'members' => $data,
            ]);
        } else {
            return response()->json([
                'code' => 0,
                'message' => 'You are not a leader',
            ], 401);
        }


    }

    public function AcceptJoin(Request $request)
    {
        $user_id = $request->user_id;
        $team_id = $request->team_id;
      if( $request->user()->isLeader == 1 && $request->user()->team_id == $team_id) {

          $existingJoin= Join::where('user_id',$user_id)->where('team_id',$team_id);

          if ($existingJoin ) {
              $existingJoin->delete();
              $user = User::find($user_id);
              $team = Team::find($team_id);
              $user->team_id = $team_id;
              $team->Num_of_Members=$team->Num_of_Members+1;
              $user->save();
              $team->save();

              return response()->json([
                  'code' => 0,
                  'message' => 'Your join request has been accepted',
              ], 200);
          } else {
              return response()->json([
                  'code' => 1,
                  'message' => 'You have not requested to join this team',
              ], 404);
          }
      }else{
          return response()->json([
              'code' => 0,
              'message' => 'You are not a leader',
          ], 401);
      }



    }

    Public function RejectJoin(Request $request){
        $user_id = $request->user_id;
        $team_id = $request->team_id;
        if( $request->user()->isLeader == 1 && $request->user()->team_id == $team_id){
            $existingJoin= Join::where('user_id',$user_id)->where('team_id',$team_id);
            if ($existingJoin ) {
                $existingJoin->delete();
                return response()->json([
                    'code' => 0,
                    'message' => 'Your join request has been rejected',
                ], 201);
            }

        }else {
            return response()->json([
                'code' => 0,
                'message' => 'You are not a leader',
            ], 401);

        }
    }
    public function DeleteMemeber(Request $request){
        $user_id=$request->user_id;
        $team_id=$request->team_id;
        $user=User::find($user_id);
        if($request->user()->isLeader==1&& $request->user()->team_id==$team_id && $user->team_id==$team_id){
            $team=Team::find($team_id);
            $user->team_id=null;
            $team->Num_of_Members=$team->Num_of_Members-1;
            $user->save();
            $team->save();
            return response()->json([
                'code' => 1,
                'message' => 'Member deleted successfully',
            ], 201);
        }else{
            return response()->json([
                'code' => 0,
                'message' => 'You are not a leader',
            ], 401);
        }

    }



}
