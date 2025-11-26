<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    // ===================================
    // LIST + FILTER
    // ===================================
    public function index(Request $request)
    {
        $query = Document::query();

        // Apply filters
        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->year) {
            $query->where('year', $request->year);
        }

        // Paginated result (10 per page)
        $docs = $query->orderBy('id', 'desc')->paginate(12);

        // Get all types & years
        $types = Document::select('type')->distinct()->pluck('type');
        $years = Document::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        // Add full URLs (optional)
        $docs->getCollection()->transform(function ($doc) {
            $doc->file_kh_url = $doc->file_kh ? asset('storage/' . $doc->file_kh) : null;
            $doc->file_en_url = $doc->file_en ? asset('storage/' . $doc->file_en) : null;
            return $doc;
        });

        return response()->json([
            'data' => $docs,
            'types' => $types,
            'years' => $years,
        ]);;
    }


    // ===================================
    // CREATE
    // ===================================
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required',
            'year' => 'required',
            'file_kh' => 'required|file|mimes:pdf,doc,docx',
            'file_en' => 'nullable|file|mimes:pdf,doc,docx',
        ]);

        $fileKH = $request->file('file_kh')->store('documents', 'public');
        $fileEN = $request->hasFile('file_en')
            ? $request->file('file_en')->store('documents', 'public')
            : null;

        $doc = Document::create([
            'title' => $request->title,
            'type' => $request->type,
            'year' => $request->year,
            'file_kh' => $fileKH,
            'file_en' => $fileEN,
        ]);

        return response()->json($doc, 201);
    }

    // ===================================
    // SHOW ONE DOCUMENT
    // ===================================
    public function show($id)
    {
        $doc = Document::findOrFail($id);
        $doc->file_kh_url = $doc->file_kh ? asset('storage/' . $doc->file_kh) : null;
        $doc->file_en_url = $doc->file_en ? asset('storage/' . $doc->file_en) : null;

        return response()->json($doc);
    }

    // ===================================
    // UPDATE
    // ===================================
    public function update(Request $request, $id)
    {
        $doc = Document::findOrFail($id);

        $request->validate([
            'title' => 'required',
            'type' => 'required',
            'year' => 'required',
            'file_kh' => 'nullable|file|mimes:pdf,doc,docx',
            'file_en' => 'nullable|file|mimes:pdf,doc,docx',
        ]);

        // Replace KH file if new uploaded
        if ($request->hasFile('file_kh')) {
            Storage::disk('public')->delete($doc->file_kh);
            $doc->file_kh = $request->file('file_kh')->store('documents', 'public');
        }

        // Replace EN file if new uploaded
        if ($request->hasFile('file_en')) {
            if ($doc->file_en) {
                Storage::disk('public')->delete($doc->file_en);
            }
            $doc->file_en = $request->file('file_en')->store('documents', 'public');
        }

        $doc->update([
            'title' => $request->title,
            'type' => $request->type,
            'year' => $request->year,
        ]);

        return response()->json($doc);
    }

    // ===================================
    // DELETE
    // ===================================
    public function destroy($id)
    {
        $doc = Document::findOrFail($id);

        Storage::disk('public')->delete($doc->file_kh);

        if ($doc->file_en) {
            Storage::disk('public')->delete($doc->file_en);
        }

        $doc->delete();

        return response()->json(['message' => 'Document deleted']);
    }
}
