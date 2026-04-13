<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Management;
use App\Models\SubManagement;
use App\Models\ModuleContent;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function globalSearch(Request $request)
    {
        $keyword = $request->keyword;
        $perPage = $request->get('per_page', 10);

        if (!$keyword) {
            return response()->json([
                'status' => false,
                'message' => 'Keyword is required'
            ], 400);
        }

        if (strlen($keyword) < 2) {
            return response()->json([
                'status' => false,
                'message' => 'Enter at least 2 characters'
            ], 400);
        }

        // 🔍 Module Content Search (MAIN with cursor pagination)
        $contentsQuery = ModuleContent::where(function ($q) use ($keyword) {
            $q->where('title', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%");
        })
            ->with(['sub.management'])
            ->orderBy('id'); // required for cursor pagination

        $contents = $contentsQuery->cursorPaginate($perPage);

        // 🔍 Management Search (limited, no cursor needed)
        $managements = Management::where('name', 'LIKE', "%$keyword%")
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'management',
                    'id' => $item->id,
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'icon' => $item->icon,
                    'image' => $item->image,
                ];
            });

        // 🔍 SubManagement Search (limited)
        $submanagements = SubManagement::where('name', 'LIKE', "%$keyword%")
            ->with('management')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'submanagement',
                    'id' => $item->id,
                    'management_id' => $item->management_id,
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'management' => $item->management->name ?? null,
                ];
            });

        // 🔥 Format Content Results
        $contentData = collect($contents->items())->map(function ($item) {
            return [
                'type' => 'content',
                'id' => $item->id,
                'management_id' => $item->sub->management->id ?? null,
                'submanagement_id' => $item->submanagement_id,
                'title' => $item->title,
                'description' => Str::limit($item->description, 100),
                'youtube_link' => $item->youtube_link,
                'pdf_file' => $item->pdf_file,
                'reading_time' => $item->reading_time,
                'submanagement' => $item->sub->name ?? null,
                'management' => $item->sub->management->name ?? null,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Search results fetched successfully',

            // 🔹 Small quick results (for UI top suggestions)
            'suggestions' => [
                'managements' => $managements,
                'submanagements' => $submanagements,
            ],

            // 🔹 Main paginated results
            'contents' => [
                'data' => $contentData,
                'next_cursor' => $contents->nextCursor() ? $contents->nextCursor()->encode() : null,
                'prev_cursor' => $contents->previousCursor() ? $contents->previousCursor()->encode() : null,
                'per_page' => $contents->perPage(),
            ]
        ]);
    }
}
