<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModuleContent;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Get single content with submanagement & management
     */
    public function show($id)
    {
        $content = ModuleContent::with('sub.management')->find($id);

        if (!$content) {
            return response()->json([
                'success' => false,
                'message' => 'Content not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Content fetched successfully',
            'data' => [
                'id' => (string) $content->id,
                'title' => $content->title,
                'summary'=> $content->summary,
                'content_in_detail' => $content->markdown_content,
                'youtube_link' => $content->youtube_link,

                // ✅ PDF full URL (adjust path if needed)
                'pdf_file' => $content->pdf_file 
                    ? asset('storage/' . $content->pdf_file)  
                    : null,
                'reading_time_in_munites' => $content->reading_time,
                'thumbnail' => null,
                'is_premium' => false,
                'created_at' => $content->created_at,

                // ✅ SubManagement Info
                'submanagement' => $content->sub ? [
                    'id' => (string) $content->sub->id,
                    'name' => $content->sub->name,
                    'slug' => $content->sub->slug,
                    'is_video_pdf' => $content->sub->is_video_pdf,
                ] : null,

                // ✅ Management Info
                'management' => ($content->sub && $content->sub->management) ? [
                    'id' => (string) $content->sub->management->id,
                    'name' => $content->sub->management->name,
                    'slug' => $content->sub->management->slug,
                    'icon' => $content->sub->management->icon,
                    'image' => $content->sub->management->image 
                        ? asset('uploads/' . $content->sub->management->image) 
                        : null,
                ] : null,
            ]
        ]);
    }
}