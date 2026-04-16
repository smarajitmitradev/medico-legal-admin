<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function list(Request $request)
    {
        $query = Notification::with('content')->latest();

        // if ID provided → single record
        if ($request->id) {
            $notification = $query->where('id', $request->id)->first();

            if (!$notification) {
                return response()->json([
                    'status' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'data' => $this->format($notification)
            ]);
        }

        // else → all list
        $notifications = $query->get();

        return response()->json([
            'status' => true,
            'data' => $notifications->map(function ($n) {
                return $this->format($n);
            })
        ]);
    }

    private function format($n)
    {
        return [
            'id' => $n->id,
            'title' => $n->title,
            'description' => $n->description,
            'type' => $n->type,
            'image' => $n->image ? asset('storage/' . $n->image) : null,

            // if content_update
            // 'content' => $n->type == 'content_update' && $n->content ? [
            //     'id' => $n->content->id,
            //     'title' => $n->content->title
            // ] : null,
            'content_id' => $n->type == 'content_update'
                ? $n->module_content_id
                : null,

            'created_at' => $n->created_at->format('Y-m-d H:i:s')
        ];
    }
}
