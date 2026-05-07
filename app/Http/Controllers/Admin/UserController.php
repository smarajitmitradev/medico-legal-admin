<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display user listing
     */
    public function index()
    {
        $users = User::latest()->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name'     => 'required|string|max:255',
            'mobile_number' => 'required|unique:users,mobile_number',
            'email'         => 'nullable|email|unique:users,email',
            'img'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageName = null;

        // Upload Image
        if ($request->hasFile('img')) {

            $image = $request->file('img');

            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('uploads'), $imageName);
        }

        User::create([
            'full_name'            => $request->full_name,
            'first_name'           => $request->first_name,
            'last_name'            => $request->last_name,
            'mobile_number'        => $request->mobile_number,
            'country_code'         => $request->country_code ?? '+91',
            'email'                => $request->email,
            'user_type'            => $request->user_type ?? 'user',
            'is_profile_complete'  => $request->is_profile_complete ?? 0,
            'is_premium'           => $request->is_premium ?? 0,
            'premium_expiry_date'  => $request->premium_expiry_date,
            'img'                  => $imageName,
            'platform'             => $request->platform,
            'app_id'               => $request->app_id,
        ]);

        return redirect()
                ->route('users.index')
                ->with('success', 'User created successfully');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'full_name'     => 'required|string|max:255',
            'mobile_number' => 'required|unique:users,mobile_number,' . $user->id,
            'email'         => 'nullable|email|unique:users,email,' . $user->id,
            'img'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageName = $user->img;

        // Upload new image
        if ($request->hasFile('img')) {

            // Delete old image
            if ($user->img && file_exists(public_path('uploads/' . $user->img))) {
                unlink(public_path('uploads/' . $user->img));
            }

            $image = $request->file('img');

            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('uploads'), $imageName);
        }

        $user->update([
            'full_name'            => $request->full_name,
            'first_name'           => $request->first_name,
            'last_name'            => $request->last_name,
            'mobile_number'        => $request->mobile_number,
            'country_code'         => $request->country_code ?? '+91',
            'email'                => $request->email,
            'user_type'            => $request->user_type ?? 'user',
            'is_profile_complete'  => $request->is_profile_complete ?? 0,
            'is_premium'           => $request->is_premium ?? 0,
            'premium_expiry_date'  => $request->premium_expiry_date,
            'img'                  => $imageName,
            'platform'             => $request->platform,
            'app_id'               => $request->app_id,
        ]);

        return redirect()
                ->route('users.index')
                ->with('success', 'User updated successfully');
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Delete image
        if ($user->img && file_exists(public_path('uploads/' . $user->img))) {
            unlink(public_path('uploads/' . $user->img));
        }

        $user->delete();

        return response()->json([
            'status'  => true,
            'message' => 'User deleted successfully'
        ]);
    }
}