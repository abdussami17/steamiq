<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'profile'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('profile')) {
            $validated['profile'] = $request->file('profile')
                ->store('organizations', 'public');
        }

        Organization::create($validated);

        return redirect()
            ->back()
            ->with('success', 'Organization created successfully!');
    }
    public function destroy(Organization $organization)
    {
        // Delete the photo file if exists
        if ($organization->profile && file_exists(storage_path('app/public/' . $organization->profile))) {
            unlink(storage_path('app/public/' . $organization->profile));
        }
    
        $organization->delete();
    
        return redirect()->back()->with('success', 'Organization deleted successfully!');
    }
    

}

