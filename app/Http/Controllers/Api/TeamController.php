<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Join;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{


    public function findTeam(Request $request)
    {
        $team_id = $request->team_id;
        $team = Team::find($team_id);
        return response()->json([
            'team' => $team
        ], 201);
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

    public function updateTeam(Request $request)
    {
        $authUser = Auth::user();
        $team_id = $request->team_id;

        if ($authUser->isLeader == 1 && $authUser->team_id == $team_id) {
            $team = Team::find($team_id);

            $request->validate([
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ]);

            $fieldsToUpdate = [];

            if (!is_null($request->name)) {
                $fieldsToUpdate['name'] = $request->name;
            }

            if (!is_null($request->description)) {
                $fieldsToUpdate['description'] = $request->description;
            }

            $team->update($fieldsToUpdate);

            return response()->json([
                'code' => 1,
                'message' => 'Team updated successfully',
                'team' => $team,
            ], 200);
        } else {
            return response()->json([
                'code' => 0,
                'message' => 'You are not the leader of this team, so you cannot edit it',
            ], 401);
        }
    }
}
