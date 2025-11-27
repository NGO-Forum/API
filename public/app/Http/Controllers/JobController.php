<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{
    public function index()
    {
        return response()->json(
            Job::get()
        );
    }

    public function show($id)
    {
        return Job::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => "required",
            "description" => "required",
            "requirements" => "nullable",
            "closing_date" => "nullable|date",
            'department' => 'required|string|in:PALI,RITI,SACHAS,MACOR',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile("image")) {
            $validated["image"] = $request->file("image")->store("jobs", "public");
        }

        $job = Job::create($validated);

        return response()->json($job, 201);
    }

    public function update(Request $request, $id)
    {
        $job = Job::findOrFail($id);

        $validated = $request->validate([
            "title" => "required",
            "description" => "required",
            "requirements" => "nullable",
            "closing_date" => "nullable|date",
            'department' => 'required|string|in:PALI,RITI,SACHAS,MACOR',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile("image")) {
            if ($job->image && Storage::disk("public")->exists($job->image)) {
                Storage::disk("public")->delete($job->image);
            }
            $validated["image"] = $request->file("image")->store("jobs", "public");
        }

        $job->update($validated);

        return response()->json($job);
    }

    public function destroy($id)
    {
        $job = Job::findOrFail($id);

        if ($job->image && Storage::disk("public")->exists($job->image)) {
            Storage::disk("public")->delete($job->image);
        }

        $job->delete();

        return response()->json(["message" => "Deleted"]);
    }
}
