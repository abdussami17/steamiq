<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\ChallengeActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SettingController extends Controller
{
    public function index()
    {
        $users = User::all();
        $cards = Card::all();
        $permissions = Permission::all();
        $roles = Role::all();
        return view('settings.index', compact('users','cards','permissions','roles'));
    }


    public function updateProfile(Request $request)
    {
        $user = auth()->user();
    
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);
    
        $user->name = $request->name;
        $user->username = $request->username;
    
        // 🔥 Check current password before updating
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Current password is incorrect'
                ], 422);
            }
    
            $user->password = bcrypt($request->password);
        }
    
        $user->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully'
        ]);
    }
    // Fetch activities via JS
    public function fetchChallengeActivities(Request $request)
    {
        $activities = ChallengeActivity::with('event')->get();

        // Return JSON
        return response()->json($activities);
    }
}