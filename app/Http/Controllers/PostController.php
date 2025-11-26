<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // GET /api/posts?limit=4
    public function index(Request $request)
    {
        $query = Post::orderBy('id', 'desc');

        // Filter by department
        if ($request->department) {
            $query->where('department', $request->department);
        }

        // Limit results
        if ($limit = $request->get('limit')) {
            return $query->take((int) $limit)->get();
        }

        return $query->paginate(9);
    }

    // GET /api/posts/{id}
    public function show(Post $post)
    {
        return response()->json($post, 200);
    }

    // POST /api/posts
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|in:PALI,RITI,SACHAS,MACOR',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'description' => 'nullable|string',
            'published_at' => 'nullable|date',
            'link' => 'nullable|url',
        ]);

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('posts', 'public');
            }
        }
        // SAVE IMAGES
        $validated['images'] = $imagePaths;
        
        $post = Post::create($validated);

        return response()->json($post, 201);
    }

    // PUT/PATCH /api/posts/{id}
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'department' => 'string|in:PALI,RITI,SACHAS,MACOR',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'description' => 'nullable|string',
            'published_at' => 'nullable|date',
            'link' => 'nullable|url',
        ]);

        $imagePaths = $post->images ?? [];

        $existing = $post->images ?? [];

        if ($request->hasFile('images')) {
            // delete old images
            foreach ($existing as $old) {
                Storage::disk('public')->delete($old);
            }

            // upload new images
            $newPaths = [];
            foreach ($request->file('images') as $img) {
                $newPaths[] = $img->store('posts', 'public');
            }

            $validated['images'] = $newPaths;
        } else {
            // keep old images
            $validated['images'] = $existing;
        }

        $validated['link'] = $request->link ?? $post->link;

        $post->update($validated);

        return response()->json($post, 200);
    }


    // DELETE /api/posts/{id}
    public function destroy(Post $post)
    {
        if ($post->images && is_array($post->images)) {
            foreach ($post->images as $img) {
                if (Storage::disk('public')->exists($img)) {
                    Storage::disk('public')->delete($img);
                }
            }
        }

        $post->delete();

        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}
