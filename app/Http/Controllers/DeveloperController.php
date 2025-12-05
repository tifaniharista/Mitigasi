<?php
// app/Http/Controllers/DeveloperController.php

namespace App\Http\Controllers;

use App\Models\Developer;
use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        // Hanya admin yang bisa akses Developers
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $query = Developer::latest();

        // Filter berdasarkan status aktif
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $developers = $query->get();

        return view('developers.index', compact('developers'));
    }

    public function create()
    {
        // Hanya admin yang bisa create
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('developers.create');
    }

    public function store(Request $request)
    {
        // Hanya admin yang bisa store
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:developers,name',
            'is_active' => 'boolean'
        ]);

        Developer::create([
            'name' => $request->name,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('developers.index')
            ->with('success', 'Developer created successfully.');
    }

    public function show(Developer $developer)
    {
        // Hanya admin yang bisa show detail
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('developers.show', compact('developer'));
    }

    public function edit(Developer $developer)
    {
        // Hanya admin yang bisa edit
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('developers.edit', compact('developer'));
    }

    public function update(Request $request, Developer $developer)
    {
        // Hanya admin yang bisa update
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:developers,name,' . $developer->id,
            'is_active' => 'boolean'
        ]);

        $developer->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('developers.index')
            ->with('success', 'Developer updated successfully.');
    }

    public function destroy(Developer $developer)
    {
        // Hanya admin yang bisa delete
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($developer->projects()->count() > 0) {
            return redirect()->route('developers.index')
                ->with('error', 'Cannot delete developer because it is used in projects.');
        }

        $developer->delete();

        return redirect()->route('developers.index')
            ->with('success', 'Developer deleted successfully.');
    }
}
