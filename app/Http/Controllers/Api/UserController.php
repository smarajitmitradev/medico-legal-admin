<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{


    public function updateProfileImage(Request $request)
    {
        $request->validate([
            'img' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $user = Auth::user();

        // Delete old image (optional)
        if ($user->img && Storage::exists($user->img)) {
            Storage::delete($user->img);
        }

        // Store new image
        $path = $request->file('img')->store('profile_images', 'public');

        // Save in DB
        $user->img = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile image updated successfully',
            'data' => [
                'image_url' => asset('storage/' . $path)
            ]
        ]);
    }
}
