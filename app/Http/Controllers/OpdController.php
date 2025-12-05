<?php
// app/Http/Controllers/OpdController.php

namespace App\Http\Controllers;

use App\Models\Opd;
use Illuminate\Http\Request;

class OpdController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['index', 'show']); // Hanya admin yang bisa CRUD selain index/show
    }

    public function index(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        
        $query = Opd::latest();

        // Filter berdasarkan status aktif
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $opds = $query->get();
        return view('opds.index', compact('opds'));
    }

    public function create()
    {
        return view('opds.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:opds,name',
            'code' => 'required|string|max:50|unique:opds,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Opd::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active') // Perbaikan di sini
        ]);

        return redirect()->route('opds.index')
            ->with('success', 'OPD created successfully.');
    }

    public function show(Opd $opd)
    {
        return view('opds.show', compact('opd'));
    }

    public function edit(Opd $opd)
    {
        return view('opds.edit', compact('opd'));
    }

    public function update(Request $request, Opd $opd)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:opds,name,' . $opd->id,
            'code' => 'required|string|max:50|unique:opds,code,' . $opd->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $opd->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => $request->has('is_active') // Perbaikan di sini
        ]);

        return redirect()->route('opds.index')
            ->with('success', 'OPD updated successfully.');
    }

    public function destroy(Opd $opd)
    {
        if ($opd->projects()->count() > 0) {
            return redirect()->route('opds.index')
                ->with('error', 'Cannot delete OPD because it is used in projects.');
        }

        $opd->delete();

        return redirect()->route('opds.index')
            ->with('success', 'OPD deleted successfully.');
    }
}
