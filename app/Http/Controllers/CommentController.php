<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    // GET /api/comments
    public function index()
    {
        return response()->json(
            Comment::latest()->get()->map(function ($comment) {
                $comment->images = $comment->images ?? [];
                return $comment;
            }),
            200
        );
    }

    // POST /api/comments
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email',
            'description' => 'nullable|string',
            'gender'      => 'nullable|in:male,female,other',
            'images.*'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('comments/images', 'public');
            }
        }

        $comment = Comment::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'description' => $validated['description'] ?? null, // ✅ FIX
            'gender'      => $validated['gender'] ?? null,
            'images'      => $imagePaths ?: null,
        ]);

        return response()->json([
            'message' => 'Comment created successfully',
            'data'    => $comment
        ], 201);
    }


    // GET /api/comments/{id}
    public function show(Comment $comment)
    {
        return response()->json($comment, 200);
    }

    // PUT /api/comments/{id}
    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'email'       => 'sometimes|email',
            'description' => 'nullable|string',
            'gender'      => 'nullable|in:male,female,other',
            'images.*'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $images = $comment->images ?? [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('comments/images', 'public');
            }
        }

        $comment->update([
            'name'        => $validated['name'] ?? $comment->name,
            'email'       => $validated['email'] ?? $comment->email,
            'description' => array_key_exists('description', $validated)
                ? $validated['description']
                : $comment->description,
            'gender'      => array_key_exists('gender', $validated)
                ? $validated['gender']
                : $comment->gender,
            'images'      => $images,
        ]);

        return response()->json([
            'message' => 'Comment updated successfully',
            'data'    => $comment
        ]);
    }

    // DELETE /api/comments/{id}
    public function destroy(Comment $comment)
    {
        if ($comment->images) {
            foreach ($comment->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
}
