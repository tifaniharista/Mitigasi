<?php

namespace App\Http\Controllers;

use App\Models\JenisDokumen;
use App\Models\Tahapan;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class JenisDokumenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $query = JenisDokumen::with(['project', 'tahapan']);
        $jenisDokumen = JenisDokumen::with(['project', 'tahapan'])->latest()->get();

        if (request()->has('filter') && request()->filter == 'overdue') {
            $query = $query->overdueWithTargetDate();
        }

        $jenisDokumen = $query->latest()->get();

        // Ambil session messages sebelum ditampilkan
        $messages = [
            'success' => session('success'),
            'error' => session('error')
        ];

        // Hapus session setelah diambil
        session()->forget(['success', 'error']);

        return view('jenis-dokumen.index', compact('jenisDokumen'))->with($messages);
    }

    public function create()
    {
        // Hanya admin dan uploader yang bisa create
        if (!auth()->user()->isAdmin() && !auth()->user()->isUploader()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek jika ada project_id di query parameter
        if (request()->has('project_id')) {
            $project = Project::findOrFail(request()->project_id);

            // Cek apakah project sudah ditutup
            if ($project->is_closed) {
                return redirect()->route('projects.show', $project)
                    ->with('error', 'Tidak dapat menambah dokumen. Project sudah ditutup.');
            }
        }

        $projects = Project::whereHas('developer', function($query) {
            $query->where('is_active', true);
        })->get();

        $tahapans = Tahapan::where('is_active', true)->ordered()->get();
        return view('jenis-dokumen.create', compact('projects', 'tahapans'));
    }

    public function store(Request $request)
    {
        // Hanya admin dan uploader yang bisa store
        if (!auth()->user()->isAdmin() && !auth()->user()->isUploader()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah project terkait sudah ditutup
        if ($request->has('project_id')) {
            $project = Project::findOrFail($request->project_id);

            if ($project->is_closed) {
                return redirect()->route('projects.show', $project)
                    ->with('error', 'Tidak dapat menambah dokumen. Project sudah ditutup.');
            }
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'tahapan_id' => 'required|exists:tahapans,id',
            'nama_dokumen' => 'required|string|max:255',
            'tanggal_realisasi' => 'nullable|date',
            'tanggal_revisi' => 'nullable|date|after_or_equal:tanggal_realisasi',
            'file_dokumen' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:2048', // 2MB = 2048KB
            'file_pendukung' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar|max:2048', // 2MB = 2048KB
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean'
        ], [
            'file_dokumen.max' => 'Ukuran file dokumen tidak boleh lebih dari 2MB.',
            'file_pendukung.max' => 'Ukuran file pendukung tidak boleh lebih dari 2MB.',
            'file_dokumen.mimes' => 'Format file dokumen harus: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG.',
            'file_pendukung.mimes' => 'Format file pendukung harus: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, ZIP, RAR.',
            'tanggal_revisi.after_or_equal' => 'Tanggal revisi harus setelah atau sama dengan tanggal realisasi.'
        ]);

        // Double check project status
        $project = Project::findOrFail($request->project_id);
        if ($project->is_closed) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Tidak dapat menambah dokumen. Project sudah ditutup.');
        }

        $data = $request->except(['file_dokumen', 'file_pendukung']);
        $data['status_verifikasi'] = 'menunggu';

        // Handle file uploads
        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $fileName = 'dokumen_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('dokumen', $fileName, 'public');
            $data['file_dokumen'] = $filePath;
        }

        if ($request->hasFile('file_pendukung')) {
            $file = $request->file('file_pendukung');
            $fileName = 'pendukung_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('dokumen', $fileName, 'public');
            $data['file_pendukung'] = $filePath;
        }

        JenisDokumen::create($data);

        return redirect()->route('jenis-dokumen.index')
            ->with('success', 'Jenis Dokumen created successfully.');
    }

    public function update(Request $request, JenisDokumen $jenisDokuman)
    {
        // Hanya admin dan uploader yang bisa update
        if (!auth()->user()->isAdmin() && !auth()->user()->isUploader()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah dokumen sudah terverifikasi (tidak bisa diupdate)
        if ($jenisDokuman->isVerified()) {
            return redirect()->route('jenis-dokumen.show', $jenisDokuman->id)
                ->with('error', 'Dokumen yang sudah terverifikasi tidak dapat diupdate.');
        }

        // Cek apakah project terkait sudah ditutup
        if ($jenisDokuman->project && $jenisDokuman->project->is_closed) {
            return redirect()->route('projects.show', $jenisDokuman->project)
                ->with('error', 'Tidak dapat mengupdate dokumen. Project sudah ditutup.');
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'tahapan_id' => 'required|exists:tahapans,id',
            'nama_dokumen' => 'required|string|max:255',
            'tanggal_realisasi' => 'nullable|date',
            'tanggal_revisi' => 'nullable|date|after_or_equal:tanggal_realisasi',
            'file_dokumen' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:2048', // 2MB = 2048KB
            'file_pendukung' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar|max:2048', // 2MB = 2048KB
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean'
        ], [
            'file_dokumen.max' => 'Ukuran file dokumen tidak boleh lebih dari 2MB.',
            'file_pendukung.max' => 'Ukuran file pendukung tidak boleh lebih dari 2MB.',
            'file_dokumen.mimes' => 'Format file dokumen harus: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG.',
            'file_pendukung.mimes' => 'Format file pendukung harus: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, ZIP, RAR.',
            'tanggal_revisi.after_or_equal' => 'Tanggal revisi harus setelah atau sama dengan tanggal realisasi.'
        ]);

        $data = $request->except(['file_dokumen', 'file_pendukung']);

        // Handle file uploads - hanya jika belum ada file dan belum terverifikasi
        if ($request->hasFile('file_dokumen')) {
            // Cek apakah file dokumen sudah ada (tidak bisa diubah jika sudah ada)
            if ($jenisDokuman->file_dokumen) {
                return redirect()->back()
                    ->with('error', 'File dokumen utama tidak dapat diubah karena sudah terupload.')
                    ->withInput();
            }

            $file = $request->file('file_dokumen');
            $fileName = 'dokumen_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('dokumen', $fileName, 'public');
            $data['file_dokumen'] = $filePath;
            $data['status_verifikasi'] = 'menunggu';
        }

        if ($request->hasFile('file_pendukung')) {
            // Cek apakah file pendukung sudah ada (tidak bisa diubah jika sudah ada)
            if ($jenisDokuman->file_pendukung) {
                return redirect()->back()
                    ->with('error', 'File pendukung tidak dapat diubah karena sudah terupload.')
                    ->withInput();
            }

            $file = $request->file('file_pendukung');
            $fileName = 'pendukung_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('dokumen', $fileName, 'public');
            $data['file_pendukung'] = $filePath;
        }

        $jenisDokuman->update($data);

        return redirect()->route('jenis-dokumen.index')
            ->with('success', 'Jenis Dokumen updated successfully.');
    }

    public function show(JenisDokumen $jenisDokuman)
    {
        $jenisDokuman->load(['project', 'tahapan', 'verifier']);
        return view('jenis-dokumen.show', compact('jenisDokuman'));
    }

    public function edit(JenisDokumen $jenisDokuman)
    {
        // Hanya admin dan uploader yang bisa edit
        if (!auth()->user()->isAdmin() && !auth()->user()->isUploader()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah dokumen sudah terverifikasi (tidak bisa diedit)
        if ($jenisDokuman->isVerified()) {
            return redirect()->route('jenis-dokumen.show', $jenisDokuman->id)
                ->with('error', 'Dokumen yang sudah terverifikasi tidak dapat diedit.');
        }

        // Cek apakah project terkait sudah ditutup
        if ($jenisDokuman->project && $jenisDokuman->project->is_closed) {
            return redirect()->route('projects.show', $jenisDokuman->project)
                ->with('error', 'Tidak dapat mengedit dokumen. Project sudah ditutup.');
        }

        $projects = Project::whereHas('developer', function($query) {
            $query->where('is_active', true);
        })->get();

        $tahapans = Tahapan::where('is_active', true)->ordered()->get();
        return view('jenis-dokumen.edit', compact('jenisDokuman', 'projects', 'tahapans'));
    }

    public function destroy(JenisDokumen $jenisDokuman)
    {
        // Hanya admin yang bisa delete
        if (!auth()->user()->isAdmin()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah project terkait sudah ditutup
        if ($jenisDokuman->project && $jenisDokuman->project->is_closed) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus dokumen. Project sudah ditutup.'
                ], 403);
            }
            return redirect()->route('projects.show', $jenisDokuman->project)
                ->with('error', 'Tidak dapat menghapus dokumen. Project sudah ditutup.');
        }

        if ($jenisDokuman->file_dokumen) {
            Storage::disk('public')->delete($jenisDokuman->file_dokumen);
        }
        if ($jenisDokuman->file_pendukung) {
            Storage::disk('public')->delete($jenisDokuman->file_pendukung);
        }

        $jenisDokuman->delete();

        // Return JSON response untuk AJAX request
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Jenis Dokumen deleted successfully.'
            ]);
        }

        return redirect()->route('jenis-dokumen.index')
            ->with('success', 'Jenis Dokumen deleted successfully.');
    }

    public function verify(Request $request, JenisDokumen $jenisDokuman)
    {
        // Hanya admin dan verifikator yang bisa verify
        if (!auth()->user()->isAdmin() && !auth()->user()->isVerificator()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah project terkait sudah ditutup
        if ($jenisDokuman->project && $jenisDokuman->project->is_closed) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat memverifikasi dokumen. Project sudah ditutup.'
                ], 403);
            }
            return redirect()->route('projects.show', $jenisDokuman->project)
                ->with('error', 'Tidak dapat memverifikasi dokumen. Project sudah ditutup.');
        }

        $request->validate([
            'status_verifikasi' => 'required|in:diterima,ditolak,menunggu',
            'catatan_verifikasi' => 'nullable|string|max:500'
        ]);

        $updateData = [
            'status_verifikasi' => $request->status_verifikasi,
            'catatan_verifikasi' => $request->catatan_verifikasi,
            'verified_by' => auth()->id()
        ];

        // Jika status bukan menunggu, set tanggal verifikasi
        if ($request->status_verifikasi !== 'menunggu') {
            $updateData['tanggal_verifikasi'] = now();
        } else {
            // Jika dikembalikan ke menunggu, reset tanggal verifikasi
            $updateData['tanggal_verifikasi'] = null;
            $updateData['verified_by'] = null;
        }

        $jenisDokuman->update($updateData);

        $statusMessages = [
            'diterima' => 'verified',
            'ditolak' => 'rejected',
            'menunggu' => 'reset to pending'
        ];

        $status = $statusMessages[$request->status_verifikasi] ?? 'updated';
        $message = "Document {$status} successfully.";

        // Return JSON response untuk AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->back()
            ->with('success', $message);
    }

    public function viewDokumen(JenisDokumen $jenisDokuman)
    {
        if (!$jenisDokuman->file_dokumen) {
            return redirect()->back()->with('error', 'File dokumen tidak ditemukan.');
        }

        if (!Storage::disk('public')->exists($jenisDokuman->file_dokumen)) {
            return redirect()->back()->with('error', 'File dokumen tidak ditemukan di server.');
        }

        // Return file response tanpa header kompleks
        return response()->file(
            Storage::disk('public')->path($jenisDokuman->file_dokumen)
        );
    }

    public function viewPendukung(JenisDokumen $jenisDokuman)
    {
        if (!$jenisDokuman->file_pendukung) {
            return redirect()->back()->with('error', 'File pendukung tidak ditemukan.');
        }

        if (!Storage::disk('public')->exists($jenisDokuman->file_pendukung)) {
            return redirect()->back()->with('error', 'File pendukung tidak ditemukan di server.');
        }

        // Return file response tanpa header kompleks
        return response()->file(
            Storage::disk('public')->path($jenisDokuman->file_pendukung)
        );
    }

    public function toggleStatus(JenisDokumen $jenisDokuman)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isUploader()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Cek apakah project terkait sudah ditutup
        if ($jenisDokuman->project && $jenisDokuman->project->is_closed) {
            return redirect()->route('projects.show', $jenisDokuman->project)
                ->with('error', 'Tidak dapat mengubah status dokumen. Project sudah ditutup.');
        }

        $jenisDokuman->update([
            'is_active' => !$jenisDokuman->is_active
        ]);

        $status = $jenisDokuman->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Jenis Dokumen {$status} successfully.");
    }

    // Method untuk menampilkan dokumen berdasarkan project
    public function byProject(Project $project)
    {
        $dokumenByTahapan = $project->dokumenByTahapan();
        return view('projects.dokumen', compact('project', 'dokumenByTahapan'));
    }

    public function downloadDokumen(JenisDokumen $jenisDokuman)
    {
        if (!$jenisDokuman->file_dokumen) {
            return redirect()->back()->with('error', 'File dokumen tidak ditemukan.');
        }

        if (!Storage::disk('public')->exists($jenisDokuman->file_dokumen)) {
            return redirect()->back()->with('error', 'File dokumen tidak ditemukan di server.');
        }

        $fileName = $jenisDokuman->nama_file_dokumen;
        return Storage::disk('public')->download($jenisDokuman->file_dokumen, $fileName);
    }

    public function downloadPendukung(JenisDokumen $jenisDokuman)
    {
        if (!$jenisDokuman->file_pendukung) {
            return redirect()->back()->with('error', 'File pendukung tidak ditemukan.');
        }

        if (!Storage::disk('public')->exists($jenisDokuman->file_pendukung)) {
            return redirect()->back()->with('error', 'File pendukung tidak ditemukan di server.');
        }

        $fileName = $jenisDokuman->nama_file_pendukung;
        return Storage::disk('public')->download($jenisDokuman->file_pendukung, $fileName);
    }

    public function bulkStatus(Request $request)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isUploader()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:jenis_dokumen,id',
            'action' => 'required|in:active,inactive'
        ]);

        // Cek apakah ada dokumen dari project yang sudah ditutup
        $closedProjectsDocs = JenisDokumen::whereIn('id', $request->ids)
            ->whereHas('project', function($query) {
                $query->where('is_closed', true);
            })->count();

        if ($closedProjectsDocs > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengubah status dokumen dari project yang sudah ditutup.'
            ], 403);
        }

        $isActive = $request->action === 'active';
        $count = JenisDokumen::whereIn('id', $request->ids)
            ->update(['is_active' => $isActive]);

        return response()->json([
            'success' => true,
            'message' => "Berhasil " . ($isActive ? 'mengaktifkan' : 'menonaktifkan') . " {$count} dokumen."
        ]);
    }
}
