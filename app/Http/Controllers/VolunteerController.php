<?php

namespace App\Http\Controllers;

use App\Models\Volunteer;
use Illuminate\Http\Request;

class VolunteerController extends Controller
{
    public function index()
    {
        return Volunteer::orderBy('id', 'desc')->get();
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);

        $imgPath = null;
        if ($request->hasFile('img')) {
            $imgPath = $request->file('img')->store('volunteers', 'public');
        }

        $volunteer = Volunteer::create([
            'name' => $request->name,
            'position' => $request->position,
            'email' => $request->email,
            'img' => $imgPath,
            'phone' => $request->phone,
            'description' => $request->description,
            'department' => $request->department,
        ]);

        return response()->json($volunteer);
    }

    public function show($id)
    {
        $volunteer = Volunteer::find($id);

        if (!$volunteer) {
            return response()->json(['message' => 'Volunteer not found'], 404);
        }

        return response()->json($volunteer);
    }

    public function update(Request $request, $id)
    {
        $volunteer = Volunteer::findOrFail($id);

        $imgPath = $volunteer->img;
        if ($request->hasFile('img')) {
            $imgPath = $request->file('img')->store('volunteers', 'public');
        }

        $volunteer->update([
            'name' => $request->name,
            'position' => $request->position,
            'email' => $request->email,
            'img' => $imgPath,
            'phone' => $request->phone,
            'description' => $request->description,
            'department' => $request->department,
        ]);

        return response()->json($volunteer);
    }

    public function destroy($id)
    {
        $volunteer = Volunteer::findOrFail($id);
        $volunteer->delete();
        return response()->json(['message' => 'Volunteer deleted']);
    }
}
