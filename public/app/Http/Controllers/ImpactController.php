<?php

namespace App\Http\Controllers;

use App\Models\Impact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImpactController extends Controller
{
    public function index()
    {
        return Impact::with('project')->orderBy('id','desc')->get();
    }

    public function show($id)
    {
        return Impact::findOrFail($id);
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name' => 'required|string',
            'summary' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',

            // ⭐ program JSON array
            'program' => 'nullable|array',
            'program.*' => 'string',

            'logo.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        // upload logos
        $logos = [];
        if ($request->hasFile('logo')) {
            foreach ($request->file('logo') as $logoFile) {
                $logos[] = $logoFile->store('impacts/logos', 'public');
            }
        }

        // upload images
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $images[] = $img->store('impacts/images', 'public');
            }
        }

        // upload file
        $filePath = $request->hasFile('file')
            ? $request->file('file')->store('impacts/files', 'public')
            : null;

        // ⭐ create impact with program JSON
        $impact = Impact::create([
            'name' => $v['name'],
            'summary' => $v['summary'] ?? null,
            'project_id' => $v['project_id'] ?? null,
            'program' => $v['program'] ?? [],   // ⭐ added
            'logo' => $logos,
            'images' => $images,
            'file' => $filePath,
        ]);

        return response()->json(['message'=>'Impact created','impact'=>$impact], 201);
    }

    public function update(Request $request, $id)
    {
        $impact = Impact::findOrFail($id);

        $v = $request->validate([
            'name' => 'required|string',
            'summary' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',

            // ⭐ program JSON array
            'program' => 'nullable|array',
            'program.*' => 'string',

            'logo.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',

            'remove_logos' => 'nullable|array',
            'remove_logos.*' => 'string',

            'remove_images' => 'nullable|array',
            'remove_images.*' => 'string',
        ]);

        // remove logos
        if ($request->filled('remove_logos')) {
            $toRemove = $request->input('remove_logos', []);
            $remaining = [];

            foreach ($impact->logo ?? [] as $p) {
                if (in_array($p, $toRemove)) {
                    Storage::disk('public')->delete($p);
                } else {
                    $remaining[] = $p;
                }
            }

            $impact->logo = $remaining;
        }

        // remove images
        if ($request->filled('remove_images')) {
            $toRemove = $request->input('remove_images', []);
            $remaining = [];

            foreach ($impact->images ?? [] as $p) {
                if (in_array($p, $toRemove)) {
                    Storage::disk('public')->delete($p);
                } else {
                    $remaining[] = $p;
                }
            }

            $impact->images = $remaining;
        }

        // add new logos
        if ($request->hasFile('logo')) {
            $new = [];
            foreach ($request->file('logo') as $logoFile) {
                $new[] = $logoFile->store('impacts/logos', 'public');
            }
            $impact->logo = array_merge($impact->logo ?? [], $new);
        }

        // add new images
        if ($request->hasFile('images')) {
            $newImages = [];
            foreach ($request->file('images') as $img) {
                $newImages[] = $img->store('impacts/images', 'public');
            }
            $impact->images = array_merge($impact->images ?? [], $newImages);
        }

        // replace main file
        if ($request->hasFile('file')) {
            if ($impact->file) {
                Storage::disk('public')->delete($impact->file);
            }
            $impact->file = $request->file('file')->store('impacts/files', 'public');
        }

        // update text fields
        $impact->name = $v['name'];
        $impact->summary = $v['summary'] ?? null;
        $impact->project_id = $v['project_id'] ?? null;

        // ⭐ update program JSON
        $impact->program = $v['program'] ?? $impact->program ?? [];

        $impact->save();

        return response()->json(['message'=>'Impact updated','impact'=>$impact]);
    }

    public function destroy($id)
    {
        $impact = Impact::findOrFail($id);

        foreach ($impact->logo ?? [] as $p) Storage::disk('public')->delete($p);
        foreach ($impact->images ?? [] as $p) Storage::disk('public')->delete($p);
        if ($impact->file) Storage::disk('public')->delete($impact->file);

        $impact->delete();

        return response()->json(['message'=>'Impact deleted']);
    }
}
