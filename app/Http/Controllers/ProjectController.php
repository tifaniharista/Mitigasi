<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Opd;
use App\Models\Developer;
use App\Models\Tahapan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Exports\ProjectsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function exportExcel(Request $request)
    {
        // Double check - hanya admin yang bisa export
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $projects = $this->getFilteredProjects($request);
        $tahun = $request->tahun;

        $filename = $tahun ? "projects_{$tahun}.xlsx" : "semua_projects.xlsx";

        return Excel::download(new ProjectsExport($projects, $tahun), $filename);
    }

    public function exportPdf(Request $request)
    {
        // Double check - hanya admin yang bisa export
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $projects = $this->getFilteredProjects($request);
        $tahun = $request->tahun;
        $tahapans = Tahapan::where('is_active', true)->ordered()->get();

        // Load relationships untuk PDF - TAMBAHKAN closedByUser
        $projects->load(['developer', 'jenisDokumen.tahapan', 'closedByUser']);

        $data = [
            'projects' => $projects,
            'tahun' => $tahun,
            'tahapans' => $tahapans,
            'exportDate' => now()->format('d/m/Y H:i:s'),
            'exportedBy' => auth()->user()->name,
        ];

        $pdf = PDF::loadView('exports.projects-pdf', $data)
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true
                ]);

        $filename = $tahun ? "projects_{$tahun}.pdf" : "semua_projects.pdf";

        return $pdf->download($filename);
    }

    private function getFilteredProjects(Request $request)
    {
        $projects = Project::with([
            'developer',
            'opdRelation',
            'jenisDokumen.tahapan',
            'closedByUser' // TAMBAHKAN INI
        ])->latest();

        // Filter berdasarkan tahun jika ada
        if ($request->has('tahun') && $request->tahun != '') {
            $tahun = $request->tahun;
            $projects->whereYear('start_date', $tahun)
                    ->orWhereYear('end_date', $tahun);
        }

        return $projects->get();
    }

    public function index(Request $request)
    {
        // Executive hanya bisa melihat, tidak bisa aksi CRUD
        $projects = Project::with([
            'developer',
            'opdRelation',
            'jenisDokumen.tahapan',
            'closedByUser'
        ])->latest();

        // Filter berdasarkan tahun jika ada
        if ($request->has('tahun') && $request->tahun != '') {
            $tahun = $request->tahun;
            $projects->whereYear('start_date', $tahun)
                    ->orWhereYear('end_date', $tahun);
        }

        $projects = $projects->get();

        // Get distinct years for dropdown
        $availableYears = Project::selectRaw('YEAR(start_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter()
            ->values();

        return view('projects.index', compact('projects', 'availableYears'));
    }

    public function create()
    {
        // Hanya admin dan uploader yang bisa create
        if (!auth()->user()->isAdmin() && !auth()->user()->isUploader()) {
            abort(403, 'Unauthorized action.');
        }

        $opds = Opd::where('is_active', true)->get();
        $developers = Developer::where('is_active', true)->get();
        return view('projects.create', compact('opds', 'developers'));
    }

    public function store(Request $request)
    {
        // Hanya admin dan uploader yang bisa store
        if (!auth()->user()->isAdmin() && !auth()->user()->isUploader()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'developer_id' => 'required|exists:developers,id',
            'opd_id' => 'required|exists:opds,id',
            'construction_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Ambil nama OPD dari ID yang dipilih
        $opd = Opd::findOrFail($request->opd_id);

        Project::create([
            'name' => $request->name,
            'developer_id' => $request->developer_id,
            'opd' => $opd->name,
            'construction_type' => $request->construction_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'actual_end_date' => null,
            'extension_reason' => null,
            'extension_days' => 0,
            'is_closed' => false,
            'closed_by' => null,
            'closed_at' => null,
            'closure_reason' => null
        ]);

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load([
            'developer',
            'opdRelation',
            'jenisDokumen.tahapan',
            'jenisDokumen.verifier',
            'closedByUser'
        ]);

        // Tambahkan pesan peringatan jika project ditutup
        if ($project->is_closed) {
            session()->flash('info', 'Project ini sudah ditutup. Penambahan dan pengeditan dokumen tidak dapat dilakukan.');
        }

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        // Hanya admin dan uploader yang bisa edit
        if (!auth()->user()->isAdmin() && !auth()->user()->isUploader()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah project sudah ditutup
        if ($project->is_closed) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Project sudah ditutup. Tidak bisa diubah.');
        }

        $opds = Opd::where('is_active', true)->get();
        $developers = Developer::where('is_active', true)->get();
        return view('projects.edit', compact('project', 'opds', 'developers'));
    }

    public function update(Request $request, Project $project)
    {
        // Hanya admin dan uploader yang bisa update
        if (!auth()->user()->isAdmin() && !auth()->user()->isUploader()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah project sudah ditutup
        if ($project->is_closed) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Project sudah ditutup. Tidak bisa diubah.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'developer_id' => 'required|exists:developers,id',
            'opd_id' => 'required|exists:opds,id',
            'construction_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'actual_end_date' => 'nullable|date|after:start_date',
            'extension_reason' => 'nullable|string|max:500',
        ]);

        // Ambil nama OPD dari ID yang dipilih
        $opd = Opd::findOrFail($request->opd_id);

        // Hitung extension days jika actual_end_date diisi
        $extensionDays = 0;
        if ($request->actual_end_date && $request->actual_end_date > $request->end_date) {
            $extensionDays = \Carbon\Carbon::parse($request->end_date)
                ->diffInDays(\Carbon\Carbon::parse($request->actual_end_date));
        }

        $project->update([
            'name' => $request->name,
            'developer_id' => $request->developer_id,
            'opd' => $opd->name,
            'construction_type' => $request->construction_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'actual_end_date' => $request->actual_end_date,
            'extension_reason' => $request->extension_reason,
            'extension_days' => $extensionDays
        ]);

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function markAsCompleted(Request $request, Project $project)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isUploader()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'actual_end_date' => 'required|date|after:start_date',
            'extension_reason' => 'nullable|string|max:500',
        ]);

        $project->markAsCompleted($request->actual_end_date);

        if ($project->is_overdue) {
            $project->update([
                'extension_reason' => $request->extension_reason
            ]);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project marked as completed successfully.');
    }

    public function showExtensionForm(Project $project)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isUploader()) {
            abort(403, 'Unauthorized action.');
        }

        return view('projects.extend', compact('project'));
    }

    // Method untuk memproses perpanjangan project
    public function extend(Request $request, Project $project)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isUploader()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'new_end_date' => 'required|date|after:start_date',
            'extension_reason' => 'required|string|max:500',
        ]);

        $project->extendProject($request->new_end_date, $request->extension_reason);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project extended successfully.');
    }

    public function destroy(Project $project)
    {
        // Hanya admin yang bisa delete
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    public function timeline(Project $project)
    {
        $project->load([
            'developer',
            'opdRelation',
            'jenisDokumen.tahapan',
            'jenisDokumen.verifier'
        ]);

        // Get timeline events
        $timelineEvents = $this->getTimelineEvents($project);

        return view('projects.timeline', compact('project', 'timelineEvents'));
    }

    public function globalTimeline()
    {
        $user = auth()->user();

        // Get all projects with their documents and relationships
        $projects = Project::with([
            'developer',
            'opdRelation',
            'jenisDokumen.tahapan',
            'jenisDokumen.verifier'
        ])->latest();

        // Filter berdasarkan OPD jika user adalah viewer dan memiliki OPD
        if ($user->isViewer() && $user->opd_id) {
            $projects->where('opd', $user->opd->name);
        }

        $projects = $projects->get();

        return view('projects.global-timeline', compact('projects'));
    }

    // Method untuk menampilkan form penutupan project
    public function showClosureForm(Project $project)
    {
        // Hanya admin yang bisa menutup project
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah project sudah ditutup
        if ($project->is_closed) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Project sudah ditutup sebelumnya.');
        }

        return view('projects.close', compact('project'));
    }

    // Method untuk menutup project
    public function closeProject(Request $request, Project $project)
    {
        // Hanya admin yang bisa menutup project
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'closure_reason' => 'nullable|string|max:500',
            'confirm_closure' => 'required|accepted',
        ]);

        // Cek apakah project sudah ditutup
        if ($project->is_closed) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Project sudah ditutup sebelumnya.');
        }

        // Tutup project
        $project->closeProject(auth()->id(), $request->closure_reason);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project berhasil ditutup dan difinalkan.');
    }

    // Method untuk membuka kembali project
    public function reopenProject(Project $project)
    {
        // Hanya admin yang bisa membuka kembali project
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek apakah project sedang ditutup
        if (!$project->is_closed) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Project belum ditutup.');
        }

        // Buka kembali project
        $project->reopenProject();

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project berhasil dibuka kembali.');
    }

    private function getTimelineEvents(Project $project)
    {
        $events = [];

        // Event: Project Created
        $events[] = [
            'type' => 'project_created',
            'title' => 'Project Dimulai',
            'description' => "Project {$project->name} resmi dimulai",
            'date' => $project->created_at->timezone('Asia/Jakarta'),
            'icon' => 'fas fa-play-circle',
            'color' => 'primary',
            'badge' => 'Project Start'
        ];

        // Event: Project Dates
        $events[] = [
            'type' => 'project_start',
            'title' => 'Tanggal Mulai Konstruksi',
            'description' => "Konstruksi fisik project dimulai",
            'date' => $project->start_date->timezone('Asia/Jakarta'),
            'icon' => 'fas fa-hard-hat',
            'color' => 'info',
            'badge' => 'Construction Start'
        ];

        // Event: Tahapan dengan dokumen-dokumennya
        $dokumenByTahapan = $project->jenisDokumen()
            ->with(['tahapan', 'verifier'])
            ->get()
            ->groupBy('tahapan_id');

        foreach ($dokumenByTahapan as $tahapanId => $tahapanDocs) {
            $tahapan = $tahapanDocs->first()->tahapan;
            $firstDoc = $tahapanDocs->sortBy('created_at')->first();

            // Event untuk tahapan (sebagai container)
            $events[] = [
                'type' => 'tahapan_container',
                'title' => "Tahap {$tahapan->nama_tahapan}",
                'description' => "Kumpulan dokumen untuk tahap {$tahapan->nama_tahapan}",
                'date' => $firstDoc->created_at->timezone('Asia/Jakarta'),
                'icon' => 'fas fa-folder',
                'color' => 'secondary',
                'badge' => 'Tahapan',
                'tahapan' => $tahapan,
                'dokumen_list' => $tahapanDocs,
                'document_count' => $tahapanDocs->count(),
                'verified_count' => $tahapanDocs->where('status_verifikasi', 'diterima')->count(),
                'pending_count' => $tahapanDocs->where('status_verifikasi', 'menunggu')->count(),
                'rejected_count' => $tahapanDocs->where('status_verifikasi', 'ditolak')->count()
            ];
        }

        // Event: Project End Date
        $events[] = [
            'type' => 'project_end',
            'title' => 'Tanggal Target Selesai',
            'description' => "Target penyelesaian project",
            'date' => $project->end_date->timezone('Asia/Jakarta'),
            'icon' => 'fas fa-flag-checkered',
            'color' => 'dark',
            'badge' => 'Target Finish'
        ];

        // Sort events by date
        usort($events, function($a, $b) {
            return $a['date'] <=> $b['date'];
        });

        return $events;
    }
}
