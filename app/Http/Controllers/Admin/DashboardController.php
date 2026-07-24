<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }


    public function ckeditor()
    {
        return view('ckeditor.ckeditor-4');
    }


    public function markdown()
    {
        return view('markdown.markdown-wysiwyg-code');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name'  => 'nullable',
            'phone'      => 'required',
        ]);
        // dd($request->last_name);
        $user = session('admin_id');
        // dd($user);

        if (!$user) {
            return back()->with('error', 'Admin not found');
        }

        $admin = \App\Models\Admin::find($user);


        $fullName = trim(
            $request->first_name . ' ' . $request->last_name
        );

        $admin->update([
            'name'  => $fullName,
            'phone' => $request->phone,
        ]);

        // refresh session
        Session::put('admin', $admin);

        return back()->with('success', 'Profile updated successfully');
    }


    // CONTROLLER METHOD
    // Profile Pic Upload
    public function avatarUpdate(Request $request)
    {
        $request->validate([
            'profile_pic' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $admin = \App\Models\Admin::find(session('admin_id'));

        if (!$admin) {
            return response()->json([
                'message' => 'Admin not found'
            ], 404);
        }

        // DELETE OLD IMAGE
        if (
            $admin->profile_pic &&
            file_exists(public_path('uploads/profile/' . $admin->profile_pic))
        ) {
            unlink(public_path('uploads/profile/' . $admin->profile_pic));
        }

        // UPLOAD NEW
        $image = $request->file('profile_pic');

        $imageName = time() . '_' . uniqid() . '.' .
            $image->getClientOriginalExtension();

        $image->move(
            public_path('uploads/profile'),
            $imageName
        );

        // SAVE
        $admin->profile_pic = $imageName;

        $admin->save();

        return response()->json([

            'status' => true,

            'message' => 'Avatar updated successfully',

            'image_url' => asset('uploads/profile/' . $imageName)
        ]);
    }
}
