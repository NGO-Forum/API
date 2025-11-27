<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\NetworkFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NetworkController extends Controller
{
    // GET all networks
    public function index()
    {
        return Network::with('files')->orderBy('id', 'asc')->get();
    }

    // CREATE network
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'slug' => 'required|string|unique:networks,slug',
        ]);

        $network = Network::create($request->only('name', 'slug'));

        return response()->json([
            'message' => 'Network created successfully',
            'network' => $network
        ]);
    }

    // UPDATE network
    public function update(Request $request, $id)
    {
        $network = Network::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'slug' => "required|string|unique:networks,slug,$id",
        ]);

        $network->update($request->only('name', 'slug'));

        return response()->json(['message' => 'Network updated']);
    }

    // DELETE network (with files)
    public function destroy($id)
    {
        $network = Network::findOrFail($id);

        // Delete related files in storage
        foreach ($network->files as $file) {
            if (strpos($file->file_url, 'storage/') !== false) {
                $storagePath = str_replace(asset('storage') . '/', '', $file->file_url);
                Storage::disk('public')->delete($storagePath);
            }
        }

        $network->delete();

        return response()->json(['message' => 'Network deleted']);
    }

    // UPLOAD file
    public function uploadFile(Request $request)
    {
        $request->validate([
            'network_id' => 'required|exists:networks,id',
            'title' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx',
        ]);

        $path = $request->file('file')->store('network-files', 'public');

        $file = NetworkFile::create([
            'network_id' => $request->network_id,
            'title' => $request->title,
            'file_url' => asset('storage/' . $path),
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'file' => $file
        ]);
    }

    // UPDATE file (replace)
    public function updateFile(Request $request, $id)
    {
        $file = NetworkFile::findOrFail($id);

        $request->validate([
            'title' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx'
        ]);

        // If new file uploaded → delete old one + store new one
        if ($request->hasFile('file')) {

            if (strpos($file->file_url, 'storage/') !== false) {
                $storagePath = str_replace(asset('storage') . '/', '', $file->file_url);
                Storage::disk('public')->delete($storagePath);
            }

            $newPath = $request->file('file')->store('network-files', 'public');
            $file->file_url = asset('storage/' . $newPath);
        }

        $file->title = $request->title;
        $file->save();

        return response()->json(['message' => 'File updated', 'file' => $file]);
    }

    // DELETE file
    public function deleteFile($id)
    {
        $file = NetworkFile::findOrFail($id);

        // delete physical file
        if (strpos($file->file_url, 'storage/') !== false) {
            $storagePath = str_replace(asset('storage') . '/', '', $file->file_url);
            Storage::disk('public')->delete($storagePath);
        }

        $file->delete();

        return response()->json(['message' => 'File deleted']);
    }
}
