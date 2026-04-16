<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\ModuleContent;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::latest()->get();
        return view('admin.notification.index', compact('notifications'));
    }

    public function create()
    {
        $contents = ModuleContent::pluck('title', 'id');
        return view('admin.notification.create', compact('contents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required|in:greeting,content_update',
            'module_content_id' => 'nullable|required_if:type,content_update'
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('notifications', 'public');
        }

        Notification::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'module_content_id' => $request->module_content_id,
            'image' => $imagePath
        ]);

        return response()->json(['message' => 'Notification Created']);
    }

    public function edit($id)
    {
        $notification = Notification::findOrFail($id);
        $contents = ModuleContent::pluck('title', 'id');

        return view('admin.notification.edit', compact('notification', 'contents'));
    }

    public function update(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);

        $request->validate([
            'title' => 'required',
            'type' => 'required|in:greeting,content_update',
            'module_content_id' => 'nullable|required_if:type,content_update'
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'module_content_id' => $request->module_content_id
        ];

        // ✅ IMAGE LOGIC
        if ($request->hasFile('image')) {

            // delete old image (optional but recommended)
            if ($notification->image && Storage::disk('public')->exists($notification->image)) {
                Storage::disk('public')->delete($notification->image);
            }

            // upload new image
            $data['image'] = $request->file('image')->store('notifications', 'public');
        }

        // if no new image → old image remains automatically

        $notification->update($data);

        return response()->json(['message' => 'Notification Updated']);
    }

    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
