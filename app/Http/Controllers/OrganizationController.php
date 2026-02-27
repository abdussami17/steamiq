<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'organization_type'    => 'required|in:School,Parks and Recreation,Youth Organization,Other',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'name.required' => 'Organization name is required.',
            'organization_type.required' => 'Organization type is required.',
            'type.in'       => 'Invalid organization type selected.',
            'profile.image' => 'Profile must be an image file.',
        ]);
    
        // custom readable errors
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first());
        }
    
        $validated = $validator->validated();
    
        // photo upload
        if ($request->hasFile('profile')) {
            $validated['profile'] = $request->file('profile')
                ->store('organizations', 'public');
        }
    
        Organization::create($validated);
    
        return back()->with('success', 'Organization created successfully!');
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
    
// Update organization
public function update(Request $request, $id)
{
    $organization = Organization::findOrFail($id);

    $data = $request->validate([
        'name' => 'required|string|max:255',
        'organization_type' => 'required|in:School,Parks and Recreation,Youth Organization,Other',
        'email' => 'nullable|email|max:255',
        'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('profile')) {
        $data['profile'] = $request->file('profile')->store('organizations', 'public');
    }

    $organization->update($data);

    return back()->with('success', 'Organization updated successfully!');
}
}

