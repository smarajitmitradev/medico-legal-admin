<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Management;
use App\Models\ModuleContent;
use App\Models\SubManagement;
use Illuminate\Http\Request;

class ManagementController extends Controller
{
    // ✅ Get all managements
    public function index()
    {
        $managements = Management::with('submanagements')->get();

        return response()->json([
            'status' => true,
            'total_managements' => Management::count(),
            'total_modules' => ModuleContent::count(),
            'data' => $managements
        ]);
    }

    // ✅ Get single management (optional)
    public function show($id)
    {
        $management = Management::with('submanagements.contents')->find($id);

        if (!$management) {
            return response()->json([
                'status' => false,
                'message' => 'Management not found'
            ], 404);
        }

        $totalModules = 0;

        foreach ($management->submanagements as $sub) {
            $totalModules += $sub->contents->count();
        }

        return response()->json([
            'status' => true,
            'total_submanagements' => $management->submanagements->count(),
            'total_modules' => $totalModules,
            'data' => $management
        ]);
    }

    public function subManagementList(Request $request)
    {
        $managementId = $request->management_id;

        if (!$managementId) {
            return response()->json([
                'success' => false,
                'message' => 'management_id is required'
            ], 400);
        }

        $subs = SubManagement::withCount('contents')
            ->where('management_id', $managementId)
            ->get();

        $data = $subs->map(function ($sub) {
            return [
                'id' => (string) $sub->id,
                'management_id' => (string) $sub->management_id,
                'title' => $sub->name,
                'image' => $sub->image ?? null, // only if column exists
                'is_premium' => false, // static (or change if you add column)
                'total_content_count' => $sub->contents_count
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Sub management list fetched successfully',
            'data' => $data
        ]);
    }
}
