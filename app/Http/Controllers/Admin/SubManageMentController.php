<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Management;
use App\Models\SubManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class SubManageMentController extends Controller
{
    // List all submanagements
    public function index()
    {
        $managements = Management::with('submanagements')->get();
        return view('admin.submanagement.index', compact('managements'));
    }

    // Show create form
    public function create()
    {
        $managements = Management::all();
        return view('admin.submanagement.create', compact('managements'));
    }

    // Store data from AJAX form
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'management_id' => 'required|exists:managements,id',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.type' => 'required|in:1,2,3',
            'items.*.link' => 'nullable|string|max:1000', // For YouTube or PDF link
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->items as $item) {
            $slug = $this->generateUniqueSlug($item['name']);

            SubManagement::create([
                'management_id' => $request->management_id,
                'name' => $item['name'],
                'slug' => $slug,
                'is_video_pdf' => $item['type'], // 1=Video,2=PDF,3=Both
                'link' => $item['link'] ?? null
            ]);
        }

        return response()->json([
            'message' => 'Sub Management(s) saved successfully!'
        ]);
    }

    // Generate unique slug
    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = SubManagement::where('slug', 'like', "{$slug}%")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    // Show edit form
    public function edit($id)
    {
        $submanagement = SubManagement::findOrFail($id);
        $managements = Management::all();
        return view('admin.submanagement.edit', compact('submanagement', 'managements'));
    }

    // Update submanagement
    public function update(Request $request, $id)
    {
        $submanagement = SubManagement::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'management_id' => 'required|exists:managements,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:1,2,3',
            'link' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $submanagement->update([
            'management_id' => $request->management_id,
            'name' => $request->name,
            'slug' => $this->generateUniqueSlug($request->name),
            'is_video_pdf' => $request->type,
            'link' => $request->link ?? null,
        ]);

        return redirect()->route('submanagement.index')
            ->with('success', 'Sub Management updated successfully!');
    }

    // Delete submanagement
    public function destroy($id)
    {
        $submanagement = SubManagement::findOrFail($id);
        $submanagement->delete();

        return redirect()->route('submanagement.index')
            ->with('success', 'Sub Management deleted successfully!');
    }
}
