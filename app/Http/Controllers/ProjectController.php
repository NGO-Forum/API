<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        // list all projects
        return response()->json(Project::orderBy('created_at', 'desc')->get());
    }

    public function show(Project $project)
    {
        return response()->json($project);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'summary'         => 'required|string',
            'objectives'      => 'nullable|array',
            'objectives.*'    => 'string',
            'duration_start'  => 'nullable|date',
            'duration_end'    => 'nullable|date',
            'target_areas'    => 'nullable|string',
            'target_groups'   => 'nullable|string',
            'donor'           => 'nullable|string|max:255',
            'key_activities'  => 'nullable|array',
            'key_activities.*' => 'string',
            'images'          => 'nullable|array',
            'images.*'        => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'department' => 'nullable|string|in:RITI,SACHAS,MACOR,PALI',
        ]);

        // Upload multiple images
        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $uploadedImages[] = $file->store('projects', 'public');
            }
        }

        $data['images'] = $uploadedImages;

        $project = Project::create($data);

        return response()->json($project, 201);
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name'            => 'sometimes|required|string|max:255',
            'summary'         => 'sometimes|required|string',
            'objectives'      => 'nullable|array',
            'objectives.*'    => 'string',
            'duration_start'  => 'nullable|date',
            'duration_end'    => 'nullable|date',
            'target_areas'    => 'nullable|string',
            'target_groups'   => 'nullable|string',
            'donor'           => 'nullable|string|max:255',
            'key_activities'  => 'nullable|array',
            'key_activities.*' => 'string',
            'removeImages'    => 'nullable|array',
            'removeImages.*'  => 'string',
            'images'          => 'nullable|array',
            'images.*'        => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'department'      => 'nullable|string|in:RITI,SACHAS,MACOR,PALI',
        ]);

        // Delete selected old images
        $remainingImages = $project->images ?? [];

        if ($request->removeImages) {
            foreach ($request->removeImages as $rem) {
                if (Storage::disk('public')->exists($rem)) {
                    Storage::disk('public')->delete($rem);
                }
                $remainingImages = array_filter($remainingImages, fn($img) => $img !== $rem);
            }
        }

        // Add newly uploaded images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $remainingImages[] = $file->store("projects", "public");
            }
        }

        $data['images'] = array_values($remainingImages);

        $project->update($data);

        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        if ($project->images) {
            foreach ($project->images as $img) {
                if (Storage::disk('public')->exists($img)) {
                    Storage::disk('public')->delete($img);
                }
            }
        }

        $project->delete();

        return response()->json(null, 204);
    }
}
