<?php

namespace App\Http\Controllers;

use App\Models\People;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PeopleController extends Controller
{
    public function index(Request $request)
    {
        $query = People::query();

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'role'        => 'nullable|string|max:255',
            'position'    => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:255',
            'img'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
            'education'   => 'nullable|array',
            'category'    => 'required|string|max:255',
        ]);

        $imgPath = null;
        if ($request->hasFile('img')) {
            $imgPath = $request->file('img')->store('people', 'public');
        }

        $People = People::create([
            ...$validated,
            'img' => $imgPath,
        ]);

        return response()->json($People, 201);
    }

    public function update(Request $request, $id)
    {
        $People = People::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'nullable|string|max:255',
            'role'        => 'nullable|string|max:255',
            'position'    => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:255',
            'img'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
            'education'   => 'nullable|array',
            'category'    => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('img')) {
            if ($People->img && Storage::disk('public')->exists($People->img)) {
                Storage::disk('public')->delete($People->img);
            }
            $validated['img'] = $request->file('img')->store('people', 'public');
        }

        $People->update($validated);

        return response()->json($People, 200);
    }

    public function destroy($id)
    {
        $People = People::findOrFail($id);

        if ($People->img && Storage::disk('public')->exists($People->img)) {
            Storage::disk('public')->delete($People->img);
        }

        $People->delete();

        return response()->json(['message' => 'Deleted successfully'], 200);
    }

    public function organizationChart()
    {
        // Get all people once
        $people = People::all();

        // Executive Director
        $executiveDirector = $people->first(function ($p) {
            return str_contains(strtolower($p->role), 'executive director');
        });

        // Dynamic department builder
        $buildDept = function ($category, $managerKeyword) use ($people) {

            $manager = $people->first(function ($p) use ($managerKeyword) {
                return str_contains(strtolower($p->role), strtolower($managerKeyword));
            });

            $staff = $people->filter(function ($p) use ($category, $managerKeyword) {
                $role = strtolower($p->role);
                return str_contains($role, strtolower($category))
                    && !str_contains($role, strtolower($managerKeyword));
            });

            return [
                "name" => strtoupper($category) . " Program",
                "manager" => $manager,
                "staff" => $staff->values(),
            ];
        };

        // Real keyword-matching based on your actual database
        $departments = [
            $buildDept("pili", "PALI Program Manager"),
            $buildDept("riti", "RITI Manager Programming"),
            $buildDept("sachas", "SACHAS Program Manager"),
            $buildDept("macor", "Operation Manager"), // FINANCE & OPERATION MANAGER
        ];

        return response()->json([
            "executiveDirector" => $executiveDirector,
            "departments" => $departments,
        ], 200);
    }

    public function media()
    {
        $people = People::all();

        // Executive Director
        $executive = $people->filter(function ($p) {
            return str_contains(strtolower($p->role), "executive director");
        });

        // Managers (exclude ED)
        $managers = $people->filter(function ($p) {
            $role = strtolower($p->role);
            return str_contains($role, "riti") && !str_contains($role, "executive director");
        });

        // Sort managers alphabetically
        $managers = $managers->sortBy('name')->values();

        // Final ordered list:
        $final = $executive->merge($managers)->values();

        return response()->json($final);
    }
}
