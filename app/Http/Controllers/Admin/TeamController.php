<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::orderBy('name')->get();

        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        return view('admin.teams.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:teams,name',
            'logo' => 'required|image',
        ]);

        $logoPath = $request->file('logo')->store('teams', 'public');

        Team::create([
            'name' => $request->name,
            'logo_path' => $logoPath,
        ]);

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Team created successfully.');
    }

    public function edit(Team $team)
    {
        return view('admin.teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:teams,name,' . $team->id,
            'logo' => 'nullable|image',
        ]);

        $data = ['name' => $request->name];

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('teams', 'public');
        }

        $team->update($data);

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        $team->delete();

        return back()->with('success', 'Team deleted.');
    }

    /**
     * Autocomplete (ya lo tenías)
     */
    public function search(Request $request)
    {
        $q = $request->query('q');

        if (!$q || strlen($q) < 2) {
            return response()->json([]);
        }

        return Team::where('name', 'like', "%{$q}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'logo_path']);
    }
}
