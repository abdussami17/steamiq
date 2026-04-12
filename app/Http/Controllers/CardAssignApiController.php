<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Group;
use App\Models\Team;
use App\Models\Student;
use Illuminate\Http\Request;

class CardAssignApiController extends Controller
{
    public function organizations()
    {
        return response()->json(Organization::select('id','name')->get());
    }

    public function groups()
    {
        return response()->json(
            Group::with('organization')
                ->select('id','group_name','organization_id')
                ->get()
        );
    }

    public function teams()
    {
        return response()->json(
            Team::with('group.organization')
                ->select('id','name','group_id')
                ->get()
        );
    }

    public function teamsByGroup($groupId)
    {
        return response()->json(
            Team::with('group.organization')
                ->where('group_id', $groupId)
                ->select('id','name','group_id')
                ->get()
        );
    }

    public function studentsByTeam($teamId)
    {
        return response()->json(Student::where('team_id', $teamId)->select('id','name')->get());
    }
}