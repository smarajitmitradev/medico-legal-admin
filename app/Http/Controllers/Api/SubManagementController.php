<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModuleContent;
use App\Helpers\ContentHelper;
require_once app_path('Helpers/ContentHelper.php');

class SubManagementController extends Controller
{
    public function contents(Request $request)
    {
        $managementId = $request->management_id;
        $subManagementId = $request->sub_management_id;
        $limit = $request->limit ?? 10;
        $cursor = $request->cursor;

        // ✅ Validation
        if (!$managementId) {
            return response()->json([
                'success' => false,
                'message' => 'management_id is required'
            ], 400);
        }

        $query = ModuleContent::query()->with('sub');

        // ✅ If sub_management_id exists → filter directly
        if ($subManagementId) {
            $query->where('submanagement_id', $subManagementId);
        } 
        // ✅ Otherwise → filter via management
        else {
            $query->whereHas('sub', function ($q) use ($managementId) {
                $q->where('management_id', $managementId);
            });
        }

        // ✅ Cursor logic
        if ($cursor) {
            $decoded = json_decode(base64_decode($cursor), true);

            if (isset($decoded['id'])) {
                $query->where('id', '>', $decoded['id']);
            }
        }

        // ✅ Fetch data
        $contents = $query->orderBy('id')
            ->limit($limit + 1)
            ->get();

        $hasMore = $contents->count() > $limit;

        $contents = $contents->take($limit);

        // ✅ Next cursor
        $nextCursor = null;
        if ($hasMore && $contents->count()) {
            $last = $contents->last();

            $nextCursor = base64_encode(json_encode([
                'id' => $last->id
            ]));
        }
        // dd(optional($contents));

        // ✅ Format response
        $data = $contents->map(function ($item) {
            return [
                'id' => (string) $item->id,
                'management_id' => (string) optional($item->sub)->management_id,
                'management_name' => (string) optional($item->sub->management)->name,
                'sub_management_id' => (string) $item->submanagement_id,
                'sub_management_name' => (string) optional($item->sub)->name,
                'title' => $item->title,
                'summary' => ContentHelper::toMarkdown($item->description),
                'reading_time_in_munites' => $item->reading_time,
                'thumbnail' => null,
                'video_url' => $item->youtube_link,
                'pdf_url' => $item->pdf_file,
                'is_premium' => false,
                'created_at' => $item->created_at
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Content list fetched successfully',
            'data' => $data,
            'paging' => [
                'next_cursor' => $nextCursor,
                'has_more' => $hasMore
            ]
        ]);
    }
}