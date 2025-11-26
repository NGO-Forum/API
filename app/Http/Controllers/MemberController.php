<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index()
    {
        return response()->json(Member::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'link' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'disabled' => 'boolean',
        ]);

        // Default disabled to false if not provided
        $data['disabled'] = $request->boolean('disabled', false);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $member = Member::create($data);

        return response()->json($member, 201);
    }

    public function update(Request $request, Member $member)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'link' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'disabled' => 'boolean',
        ]);

        // Default false if no value passed
        if ($request->has('disabled')) {
            $data['disabled'] = $request->boolean('disabled');
        }

        if ($request->hasFile('logo')) {
            if ($member->logo && Storage::disk('public')->exists($member->logo)) {
                Storage::disk('public')->delete($member->logo);
            }

            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $member->update($data);

        return response()->json($member);
    }

    public function destroy(Member $member)
    {
        if ($member->logo && Storage::disk('public')->exists($member->logo)) {
            Storage::disk('public')->delete($member->logo);
        }

        $member->delete();

        return response()->json(null, 204);
    }
}
