<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Management;
use App\Models\SubManagement;
use App\Models\ModuleContent;
use League\CommonMark\CommonMarkConverter;
use Illuminate\Database\QueryException;
use Exception;

class ModuleController extends Controller
{
    // MAIN PAGE (NOW DROPDOWN BASED)
    public function index($slug = null)
    {
        $managements = Management::all();

        return view('admin.module.index', compact('managements'));
    }

    // GET SUB MANAGEMENT (AJAX)
    public function getSubManagement($id)
    {
        $subs = SubManagement::where('management_id', $id)->get();
        return response()->json($subs);
    }

    // LOAD MODULES USING SLUG (IMPORTANT CHANGE)

    public function getModules(Request $request)
    {
        $sub = SubManagement::find($request->submanagement_id);

        if (!$sub) {
            return '<tr><td colspan="4" class="text-center text-danger">Invalid SubManagement</td></tr>';
        }

        $modules = ModuleContent::where('submanagement_id', $sub->id)
            ->latest()
            ->get();

        $converter = new CommonMarkConverter();

        foreach ($modules as $module) {
            // Step 1: Convert placeholders to symbols BEFORE Markdown
            $desc = $module->description;
            $desc = str_replace('[OK]', '✅', $desc);
            $desc = str_replace('->', '→', $desc);
            $desc = str_replace('- >', '→', $desc); // handles your exact case
            $desc = str_replace('—', '-', $desc);   // optional: normalize dash

            // Step 2: Convert to HTML using CommonMark
            $module->description_html = $converter->convert($desc);
        }

        return view('admin.module.table', compact('modules', 'sub'))->render();
    }

    // DELETE (UNCHANGED)
    public function destroy($slug, $id)
    {
        ModuleContent::findOrFail($id)->delete();
        return back()->with('success', 'Deleted');
    }

    public function indexPage()
    {
        $managements = Management::all();

        return view('admin.module.index', compact('managements'));
    }


    public function create($sub_slug)
    {
        $sub = SubManagement::where('slug', $sub_slug)->firstOrFail();

        return view('admin.module.create', compact('sub'));
    }

    public function store(Request $request, $sub_slug)
    {
        try {
            $sub = SubManagement::where('slug', $sub_slug)->firstOrFail();

            // Normalize + Safe Replace
            if ($request->has('description') && $request->description != null) {
                $desc = $request->description;

                // Replace problematic Unicode characters
                $desc = str_replace('✅', '[OK]', $desc);
                $desc = str_replace('→', '->', $desc);
                $desc = str_replace('—', '-', $desc);

                // Normalize bullets
                $desc = str_replace(["•", "‣", "⁃"], '-', $desc);

                // Ensure proper markdown format
                $desc = preg_replace('/^\s*[-–—]\s*/m', '- ', $desc);

                $request->merge(['description' => $desc]);
            }

            $request->validate([
                'title' => 'required',
                'description' => 'nullable',
                'youtube_link' => 'nullable',
                'pdf_file' => 'nullable|file|mimes:pdf',
            ]);

            $module = new ModuleContent();
            $module->title = $request->title;
            $module->description = $request->description;
            $module->youtube_link = $request->youtube_link;
            $module->submanagement_id = $sub->id;

            // Upload PDF
            if ($request->hasFile('pdf_file')) {
                $module->pdf_file = $request->file('pdf_file')->store('pdfs', 'public');
            }

            $module->save();

            return redirect()
                ->route('module.index', $sub->slug)
                ->with('success', 'Module created successfully');
        } catch (QueryException $e) {
            return back()->with('error', 'DB Error: ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);

        $path = $request->file('image')->store('editor-images', 'public');

        return response()->json([
            'url' => asset('storage/' . $path)
        ]);
    }
}
