<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Management;

class ManagementController extends Controller
{
    // ✅ Get all managements
    public function index()
    {
        $managements = Management::with('submanagements')->get();

        return response()->json([
            'status' => true,
            'data' => $managements
        ]);
    }

    // ✅ Get single management (optional)
    public function show($id)
    {
        $management = Management::with('submanagements')->find($id);

        if (!$management) {
            return response()->json([
                'status' => false,
                'message' => 'Management not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $management
        ]);
    }
}