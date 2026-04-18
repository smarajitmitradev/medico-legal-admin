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
use Illuminate\Support\Facades\Storage;

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
            // ✅ Move "o" to new line WITHOUT removing . or :
            $desc = preg_replace('/([.:])\s+\bo\b\s+/i', "$1\no ", $desc);

            // standalone "o" only (not inside words)
            $desc = preg_replace('/(?<!\n)\bo\b\s+/i', "\no ", $desc);
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

            // ✅ RAW markdown input
            $rawContent = $request->description;

            // ✅ Structured markdown (for API)
            $markdownContent = $this->formatToMarkdown($rawContent);

            // ✅ Clean description (no change in your logic)
            $desc = $rawContent;

            if (!empty($desc)) {
                $desc = str_replace('✅', '[OK]', $desc);
                $desc = str_replace('→', '->', $desc);
                $desc = str_replace('—', '-', $desc);
                $desc = str_replace(["•", "‣", "⁃"], '-', $desc);
                $desc = preg_replace('/^\s*[-–—]\s*/m', '- ', $desc);
            }

            $request->validate([
                'title' => 'required|required',
                'description' => 'nullable|required',
                'summary' => 'nullable|required',
                'youtube_link' => 'nullable|required',
                'pdf_file' => 'nullable|file|mimes:pdf',
                'reading_time' => 'required|integer|min:1'
            ]);

            $module = new ModuleContent();
            $module->title = $request->title;

            // ✅ Keep your description logic unchanged (just cleaner assignment)
            $module->description = $desc;

            // ✅ Structured markdown
            $module->markdown_content = $markdownContent;

            $module->youtube_link = $request->youtube_link;
            $module->submanagement_id = $sub->id;
            $module->summary = $request->summary;
            $module->reading_time = $request->reading_time;

            // PDF Upload
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

    public function update(Request $request, $sub_slug, $id)
    {
        try {
            $module = ModuleContent::findOrFail($id);

            // ✅ RAW input
            $rawContent = $request->description;

            // ✅ Structured markdown (only if provided)
            $markdownContent = !empty($rawContent)
                ? $this->formatToMarkdown($rawContent)
                : $module->markdown_content;
            // dd($markdownContent);
            // ✅ Clean description (same logic as store)
            $desc = $rawContent;

            if (!empty($desc)) {

                // Normalize encoding (fix weird characters like ÔÇ£)
                $desc = mb_convert_encoding($desc, 'UTF-8', 'UTF-8');

                // Replace common problematic Unicode characters
                $search = [
                    '✅', '→', '—', '–',
                    '“', '”', '‘', '’',
                    '•', '‣', '⁃',
                    '…'
                ];

                $replace = [
                    '[OK]', '->', '-', '-',
                    '"', '"', "'", "'",
                    '-', '-', '-',
                    '...'
                ];

                $desc = str_replace($search, $replace, $desc);

                // Remove any remaining non-UTF8 / unsupported chars
                $desc = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $desc);

                // Clean bullet formatting
                // $desc = preg_replace('/^\s*[-–—]\s*/m', '- ', $desc);
                $desc = preg_replace('/^([ ]*)[-–—]\s*/m', '$1- ', $desc);
            } else {
                $desc = $module->description;
            }

            $request->validate([
                'title' => 'required',
                'description' => 'nullable',
                'youtube_link' => 'nullable',
                'pdf_file' => 'nullable|file|mimes:pdf',
                'reading_time' => 'required|integer|min:1',
                'summary' => 'nullable|required',
            ]);
            // ✅ Update main fields
            $module->update([
                'title' => $request->title,
                'description' => $desc,                  // cleaned
                'markdown_content' => $markdownContent,  // structured markdown
                'youtube_link' => $request->youtube_link,
                'reading_time' => $request->reading_time,
                'summary' => $request->summary,
            ]);

            // ✅ PDF Update
            if ($request->hasFile('pdf_file')) {

                if ($module->pdf_file && Storage::disk('public')->exists($module->pdf_file)) {
                    Storage::disk('public')->delete($module->pdf_file);
                }

                $module->update([
                    'pdf_file' => $request->file('pdf_file')->store('pdfs', 'public')
                ]);
            }

            return redirect()
                ->route('module.index', $module->sub->slug)
                ->with('success', 'Module updated successfully');
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($sub_slug, $id)
    {
        $module = ModuleContent::findOrFail($id);

        return view('admin.module.edit', compact('module'));
    }

    private function formatToMarkdown($text)
    {
        if (!$text) return null;

        $lines = preg_split('/\r\n|\r|\n/', $text);
        $formatted = [];

        foreach ($lines as $line) {
            $line = rtrim($line); // keep indentation

            if ($line === '') {
                $formatted[] = '';
                continue;
            }

            // ✅ Handle "*" bullets (with indentation)
            if (preg_match('/^([ ]*)\*\s+/', $line, $matches)) {
                $indent = $matches[1];
                $line = $indent . '- ' . preg_replace('/^([ ]*)\*\s+/', '', $line);
                $formatted[] = $line;
                continue;
            }

            // ✅ Handle (• ‣ ⁃)
            if (preg_match('/^([ ]*)(•|‣|⁃)\s+/u', $line, $matches)) {
                $indent = $matches[1];
                $line = $indent . '- ' . preg_replace('/^([ ]*)(•|‣|⁃)\s+/u', '', $line);
                $formatted[] = $line;
                continue;
            }

            // ✅ Handle "o" sub-bullets
            if (preg_match('/^([ ]*)o\s+/u', $line, $matches)) {
                $indent = $matches[1] . '  '; // force sub-level
                $line = $indent . '- ' . preg_replace('/^([ ]*)o\s+/u', '', $line);
                $formatted[] = $line;
                continue;
            }

            // ✅ Normalize dash bullets (keep indentation)
            if (preg_match('/^([ ]*)[-–—]\s+/', $line, $matches)) {
                $indent = $matches[1];
                $line = $indent . '- ' . preg_replace('/^([ ]*)[-–—]\s+/', '', $line);
                $formatted[] = $line;
                continue;
            }

            // ✅ Headings
            if (stripos($line, 'Definition:') === 0) {
                $formatted[] = "## Definition";
                $formatted[] = trim(substr($line, strlen('Definition:')));
                continue;
            }

            if (stripos($line, 'Standard applied:') === 0) {
                $formatted[] = "## Standard Applied";
                $formatted[] = trim(substr($line, strlen('Standard applied:')));
                continue;
            }

            // ✅ Key Points
            if (
                str_contains($line, 'Misdiagnosis') ||
                str_contains($line, 'Ignoring') ||
                str_contains($line, 'Civil negligence')
            ) {
                if (!in_array("## Key Points", $formatted)) {
                    $formatted[] = "## Key Points";
                }

                $formatted[] = "- " . ltrim($line, '- ');
                continue;
            }

            $formatted[] = $line;
        }

        return implode("\n", $formatted);
    }
}
