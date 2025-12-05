<?php

namespace App\Http\Controllers;

use App\Models\Tahapan;
use Illuminate\Http\Request;

class TahapanController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['index', 'show']);
    }

    public function index()
    {
        // Hanya admin yang bisa akses Tahapan
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $tahapans = Tahapan::ordered()->get();
        return view('tahapans.index', compact('tahapans'));
    }

    public function create()
    {
        $lastOrder = Tahapan::max('order') ?? 0;
        return view('tahapans.create', compact('lastOrder'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tahapan' => 'required|string|max:255|unique:tahapans,nama_tahapan',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        Tahapan::create([
            'nama_tahapan' => $request->nama_tahapan,
            'order' => $request->order,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('tahapans.index')
            ->with('success', 'Tahapan created successfully.');
    }

    public function show(Tahapan $tahapan)
    {
        return view('tahapans.show', compact('tahapan'));
    }

    public function edit(Tahapan $tahapan)
    {
        return view('tahapans.edit', compact('tahapan'));
    }

    public function update(Request $request, Tahapan $tahapan)
    {
        $request->validate([
            'nama_tahapan' => 'required|string|max:255|unique:tahapans,nama_tahapan,' . $tahapan->id,
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $tahapan->update([
            'nama_tahapan' => $request->nama_tahapan,
            'order' => $request->order,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('tahapans.index')
            ->with('success', 'Tahapan updated successfully.');
    }

    public function destroy(Tahapan $tahapan)
    {
        $tahapan->delete();

        return redirect()->route('tahapans.index')
            ->with('success', 'Tahapan deleted successfully.');
    }

    public function toggleStatus(Tahapan $tahapan)
    {
        $tahapan->update([
            'is_active' => !$tahapan->is_active
        ]);

        $status = $tahapan->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Tahapan {$status} successfully.");
    }

    public function updateOrder(Request $request)
    {
        // Validasi input
        $request->validate([
            'tahapans' => 'required|array',
            'tahapans.*.id' => 'required|exists:tahapans,id',
            'tahapans.*.order' => 'required|integer|min:0'
        ]);

        try {
            // Update order untuk setiap tahapan
            foreach ($request->tahapans as $tahapanData) {
                Tahapan::where('id', $tahapanData['id'])->update([
                    'order' => $tahapanData['order']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tahapan order updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage()
            ], 500);
        }
    }
}
