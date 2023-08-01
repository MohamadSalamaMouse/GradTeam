<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;

class ShowController extends Controller
{
    public function ShowAll()
{
    $users = User::orderByDesc('id')->get();
    $teams = Team::orderByDesc('id')->get();

    $count = [];
    foreach ($teams as $team) {
        $count[$team->id] = $team->members->count();
    }

    return response()->json([
        'users' => $users,
        'teams' => $teams,
        'count' => $count
    ], 200);
}




}

