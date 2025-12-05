<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\JenisDokumen;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Redirect jika viewer belum terverifikasi atau ditolak
        if ($user->isViewer() && !$user->isVerified()) {
            return redirect()->route('waiting-verification');
        }

        if ($user->isRejected()) {
            return redirect()->route('rejection.show');
        }

        // Data berdasarkan role
        if ($user->isViewer() && $user->opd_id) {
            return $this->getViewerDashboard($user);
        } elseif ($user->isAdmin() || $user->isExecutive()) {
            return $this->getAdminExecutiveDashboard($user);
        } elseif ($user->isVerificator()) {
            return $this->getVerificatorDashboard($user);
        } elseif ($user->isUploader()) {
            return $this->getUploaderDashboard($user);
        }

        return $this->getDefaultDashboard();
    }

    private function getAdminExecutiveDashboard($user)
    {
        try {
            // Basic statistics
            $totalProjects = Project::count();
            $ongoingProjects = Project::where('end_date', '>=', now())->count();
            $completedProjects = Project::where('end_date', '<', now())->count();
            $totalUsers = User::count();

            // Overdue documents count for all roles except viewer
            $overdueDocuments = $this->getOverdueDocumentsCount();
            $overdueDocumentsList = $this->getOverdueDocuments(5); // Ambil 5 terbaru

            // Project status data for chart
            $projectStatusData = [
                'labels' => ['Berlangsung', 'Selesai', 'Lainnya'],
                'data' => [
                    $ongoingProjects,
                    $completedProjects,
                    $totalProjects - ($ongoingProjects + $completedProjects)
                ]
            ];

            // Documents monthly data
            $documentsMonthlyData = $this->getDocumentsMonthlyData();

            // Recent projects
            $recentProjects = Project::with(['developer', 'opdRelation'])
                ->latest()
                ->take(5)
                ->get();

            // Pending verification count
            $pendingVerification = JenisDokumen::where('status_verifikasi', 'menunggu')->count();

            return view('dashboard', compact(
                'totalProjects',
                'ongoingProjects',
                'completedProjects',
                'totalUsers',
                'projectStatusData',
                'documentsMonthlyData',
                'recentProjects',
                'pendingVerification',
                'overdueDocuments',
                'overdueDocumentsList'
            ));

        } catch (\Exception $e) {
            return $this->getDefaultDashboard();
        }
    }

    private function getVerificatorDashboard($user)
    {
        try {
            $pendingVerification = JenisDokumen::where('status_verifikasi', 'menunggu')->count();
            $verifiedDocuments = JenisDokumen::where('status_verifikasi', 'diterima')->count();
            $rejectedDocuments = JenisDokumen::where('status_verifikasi', 'ditolak')->count();
            $totalDocuments = JenisDokumen::count();

            // Overdue documents count for verificator
            $overdueDocuments = $this->getOverdueDocumentsCount();
            $overdueDocumentsList = $this->getOverdueDocuments(5); // Ambil 5 terbaru

            $recentProjects = Project::with(['developer', 'opdRelation'])
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard', compact(
                'pendingVerification',
                'verifiedDocuments',
                'rejectedDocuments',
                'totalDocuments',
                'recentProjects',
                'overdueDocuments',
                'overdueDocumentsList'
            ));

        } catch (\Exception $e) {
            return $this->getDefaultDashboard();
        }
    }

    private function getUploaderDashboard($user)
    {
        try {
            $myDocuments = JenisDokumen::count(); // Bisa ditambahkan filter by user jika diperlukan
            $myPendingDocuments = JenisDokumen::where('status_verifikasi', 'menunggu')->count();
            $myVerifiedDocuments = JenisDokumen::where('status_verifikasi', 'diterima')->count();
            $myRejectedDocuments = JenisDokumen::where('status_verifikasi', 'ditolak')->count();

            // Overdue documents count for uploader
            $overdueDocuments = $this->getOverdueDocumentsCount();
            $overdueDocumentsList = $this->getOverdueDocuments(5); // Ambil 5 terbaru

            $recentProjects = Project::with(['developer', 'opdRelation'])
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard', compact(
                'myDocuments',
                'myPendingDocuments',
                'myVerifiedDocuments',
                'myRejectedDocuments',
                'recentProjects',
                'overdueDocuments',
                'overdueDocumentsList'
            ));

        } catch (\Exception $e) {
            return $this->getDefaultDashboard();
        }
    }

    private function getViewerDashboard($user)
    {
        try {
            $opdName = $user->opd->name;

            $opdProjectsCount = Project::where('opd', $opdName)->count();
            $ongoingProjectsCount = Project::where('opd', $opdName)
                ->where('end_date', '>=', now())->count();
            $completedProjectsCount = Project::where('opd', $opdName)
                ->where('end_date', '<', now())->count();

            $documentsCount = JenisDokumen::whereHas('project', function($query) use ($opdName) {
                $query->where('opd', $opdName);
            })->count();

            $chartData = $this->getViewerChartData($user);

            $recentProjects = Project::where('opd', $opdName)
                ->with(['developer', 'opdRelation'])
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard', compact(
                'opdProjectsCount',
                'ongoingProjectsCount',
                'completedProjectsCount',
                'documentsCount',
                'chartData',
                'recentProjects'
            ));

        } catch (\Exception $e) {
            return $this->getDefaultDashboard();
        }
    }

    private function getDocumentsMonthlyData()
    {
        $data = ['labels' => [], 'data' => []];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthName = $month->format('M Y');

            $count = JenisDokumen::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $data['labels'][] = $monthName;
            $data['data'][] = $count;
        }

        return $data;
    }

    private function getViewerChartData($user)
    {
        $opdName = $user->opd->name;

        $projects = Project::where('opd', $opdName)
            ->orderBy('start_date')
            ->get();

        $chartData = ['labels' => [], 'data' => []];

        if ($projects->isEmpty()) return $chartData;

        $monthlyData = [];
        foreach ($projects as $project) {
            $yearMonth = $project->start_date->format('M Y');
            if (!isset($monthlyData[$yearMonth])) {
                $monthlyData[$yearMonth] = 0;
            }
            $monthlyData[$yearMonth]++;
        }

        $recentMonths = array_slice($monthlyData, -12, 12, true);

        foreach ($recentMonths as $month => $count) {
            $chartData['labels'][] = $month;
            $chartData['data'][] = $count;
        }

        return $chartData;
    }

    private function getDefaultDashboard()
    {
        try {
            $totalProjects = Project::count();
            $ongoingProjects = Project::where('end_date', '>=', now())->count();
            $completedProjects = Project::where('end_date', '<', now())->count();
            $recentProjects = Project::with(['developer', 'opdRelation'])->latest()->take(5)->get();

            // Overdue documents for default dashboard
            $overdueDocuments = $this->getOverdueDocumentsCount();
            $overdueDocumentsList = $this->getOverdueDocuments(5);

            return view('dashboard', compact(
                'totalProjects',
                'ongoingProjects',
                'completedProjects',
                'recentProjects',
                'overdueDocuments',
                'overdueDocumentsList'
            ));

        } catch (\Exception $e) {
            $totalProjects = 0;
            $ongoingProjects = 0;
            $completedProjects = 0;
            $recentProjects = collect();
            $overdueDocuments = 0;
            $overdueDocumentsList = collect();

            return view('dashboard', compact(
                'totalProjects',
                'ongoingProjects',
                'completedProjects',
                'recentProjects',
                'overdueDocuments',
                'overdueDocumentsList'
            ));
        }
    }

    /**
     * Get count of overdue documents
     */
    private function getOverdueDocumentsCount()
    {
        return JenisDokumen::overdueWithTargetDate()->count();
    }

    /**
     * Get list of overdue documents
     */
    private function getOverdueDocuments($limit = 5)
    {
        return JenisDokumen::overdueWithTargetDate()
            ->with(['project', 'tahapan'])
            ->orderBy('tanggal_realisasi', 'asc')
            ->limit($limit)
            ->get()
            ->map(function($doc) {
                // Hitung keterlambatan dalam hari
                $targetDate = $doc->tanggal_realisasi ? Carbon::parse($doc->tanggal_realisasi) : null;
                $overdueDays = $targetDate ? now()->diffInDays($targetDate, false) * -1 : 0;

                $doc->overdue_days = $overdueDays > 0 ? $overdueDays : 0;
                return $doc;
            });
    }
}
