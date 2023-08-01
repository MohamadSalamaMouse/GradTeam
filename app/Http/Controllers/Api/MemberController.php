<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Join;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{

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

    public function leaveTeam(Request $request)
    {
        $authUser = Auth::user();
        $team_id = $request->team_id;

        if ($authUser->team_id == $team_id) {
            $team = Team::find($team_id);
            $team->Num_of_Members = $team->Num_of_Members - 1;
            $team->save();

            $authUser->team_id = null;
            $authUser->save();

            return response()->json([
                'code' => 1,
                'message' => 'You have left the team successfully',
            ], 200);
        } else {
            return response()->json([
                'code' => 0,
                'message' => 'You are not a member of this team, so you cannot leave it',
            ], 401);
        }
    }
}
