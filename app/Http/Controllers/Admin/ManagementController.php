<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Management;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ManagementController extends Controller
{
    public function index()
    {
        $managements = Management::latest()->get();
        return view('admin.management.index', compact('managements'));
    }

    public function create()
    {
        return view('admin.management.create');
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name'  => 'required|unique:managements,name',
            'icon'  => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:3000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $slug = Str::slug($request->name);
        $original = $slug;
        $count = 1;

        while (Management::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count++;
        }

        $imageName = null;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->move(public_path('uploads'), $imageName);
        }

        Management::create([
            'name'  => $request->name,
            'slug'  => $slug,
            'icon'  => $request->icon,
            'image' => $imageName
        ]);

        
        return response()->json([
            'status'  => true,
            'message' => 'Management created successfully!'
        ]);
    }

    public function edit($id)
    {
        $management = Management::findOrFail($id);
        return view('admin.management.edit', compact('management'));
    }

    public function update(Request $request, $id)
    {
        $management = Management::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'  => 'required|unique:managements,name,' . $id,
            'icon'  => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:3000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $slug = Str::slug($request->name);
        $original = $slug;
        $count = 1;

        while (
            Management::where('slug', $slug)
                ->where('id', '!=', $id)
                ->exists()
        ) {
            $slug = $original . '-' . $count++;
        }

    
        $imageName = $management->image;

        if ($request->hasFile('image')) {

            // Delete old image (important)
            if ($management->image && file_exists(public_path('uploads/' . $management->image))) {
                unlink(public_path('uploads/' . $management->image));
            }

            // Upload new image
            $imageName = time() . '_' . $request->image->getClientOriginalName();
            $request->image->move(public_path('uploads'), $imageName);
        }

        $management->update([
            'name'  => $request->name,
            'slug'  => $slug,
            'icon'  => $request->icon,
            'image' => $imageName
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Management updated successfully!'
        ]);
    }

    public function destroy($id)
    {
        $management = Management::findOrFail($id);

        // Delete image if exists
        if($management->image && file_exists(public_path('uploads/'.$management->image))){
            unlink(public_path('uploads/'.$management->image));
        }

        $management->delete();

        return response()->json([
            'status' => true,
            'message' => 'Management deleted successfully!'
        ]);
    }
}