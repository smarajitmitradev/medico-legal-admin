<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function list(Request $request)
    {
        $limit = $request->limit ?? 10;
        $cursor = $request->cursor;

        $query = Notification::with('content')->orderBy('id');

        // ✅ Single record (no pagination)
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

        // ✅ Cursor logic
        if ($cursor) {
            $decoded = json_decode(base64_decode($cursor), true);

            if (isset($decoded['id'])) {
                $query->where('id', '>', $decoded['id']);
            }
        }

        // ✅ Fetch records
        $notifications = $query->limit($limit + 1)->get();

        $hasMore = $notifications->count() > $limit;

        $notifications = $notifications->take($limit);

        // ✅ Next cursor
        $nextCursor = null;
        if ($hasMore && $notifications->count()) {
            $last = $notifications->last();

            $nextCursor = base64_encode(json_encode([
                'id' => $last->id
            ]));
        }

        // ✅ Response
        return response()->json([
            'status' => true,
            'data' => $notifications->map(function ($n) {
                return $this->format($n);
            }),
            'paging' => [
                'next_cursor' => $nextCursor,
                'has_more' => $hasMore
            ]
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
