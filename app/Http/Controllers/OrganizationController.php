<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'event_id' => 'required|integer|exists:events,id',
            'organization_type' => 'required|in:School,Parks and Recreation,Youth Organization,Other',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'event_id.required' => 'Selection Of Event is required',
            'name.required' => 'Organization name is required.',
            'organization_type.required' => 'Organization type is required.',
            'type.in' => 'Invalid organization type selected.',
            'profile.image' => 'Profile must be an image file.',
        ]);
    
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first());
        }
    
        $validated = $validator->validated();
    
        if ($request->hasFile('profile')) {
    
            $file = $request->file('profile');
    
            $extension = $file->getClientOriginalExtension() ?: $file->extension();
            $filename = time().'_'.Str::random(8).'.'.$extension;
    
            $destinationDir = public_path('storage/organization');
    
            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }
    
            $file->move($destinationDir, $filename);
    
            $validated['profile'] = 'organization/'.$filename;
        }
    
        Organization::create($validated);
    
        return redirect()->back()
            ->with('active_tab', 'groups-tab')
            ->with('success', 'Organization created successfully!');
    }
    public function destroy(Organization $organization)
    {
    
    
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
        'event_id'   => 'required|integer|exists:events,id',
    ]);

    if ($request->hasFile('profile')) {
        $data['profile'] = $request->file('profile')->store('organizations', 'public');
    }

    $organization->update($data);

    return back()->with('success', 'Organization updated successfully!');
}
public function bulkDelete(Request $request)
{
    $ids = $request->ids;

    if (!is_array($ids) || empty($ids)) {
        return response()->json(['success' => false]);
    }

    Organization::whereIn('id', $ids)->delete();

    return response()->json(['success' => true]);
}   
}

