<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();

        // Hitung jumlah user yang menunggu verifikasi
        $pendingCount = User::where('status_verifikasi', User::STATUS_PENDING)
            ->where('role', User::ROLE_VIEWER)
            ->count();

        return view('users.index', compact('users', 'pendingCount'));
    }

    public function create()
    {
        $roles = [
            User::ROLE_ADMIN => 'Administrator',
            User::ROLE_EXECUTIVE => 'Eksekutif',
            User::ROLE_VERIFICATOR => 'Verifikator',
            User::ROLE_UPLOADER => 'Uploader',
            User::ROLE_VIEWER => 'Viewer'
        ];

        $opds = \App\Models\Opd::orderBy('name')->get();

        return view('users.create', compact('roles', 'opds'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,executive,verificator,uploader,viewer',
            'opd_id' => 'nullable|exists:opds,id',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'opd_id.exists' => 'OPD yang dipilih tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi.');
        }

        try {
            // Untuk user viewer yang dibuat admin, langsung approved
            $statusVerifikasi = $request->role === User::ROLE_VIEWER ? User::STATUS_APPROVED : null;

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'opd_id' => $request->opd_id,
                'is_active' => $request->has('is_active'),
                'status_verifikasi' => $statusVerifikasi,
            ]);

            Log::info('User created by admin', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id
            ]);

            return redirect()->route('users.index')
                ->with('success', 'User berhasil ditambahkan!');

        } catch (\Exception $e) {
            Log::error('User creation failed', [
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menambahkan user: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(User $user)
    {
        $user->load(['verifiedDocuments' => function($query) {
            $query->latest()->take(10);
        }]);

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = [
            User::ROLE_ADMIN => 'Administrator',
            User::ROLE_EXECUTIVE => 'Eksekutif',
            User::ROLE_VERIFICATOR => 'Verifikator',
            User::ROLE_UPLOADER => 'Uploader',
            User::ROLE_VIEWER => 'Viewer'
        ];

        // Tambahkan data OPD
        $opds = \App\Models\Opd::orderBy('name')->get();

        return view('users.edit', compact('user', 'roles', 'opds'));
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|in:admin,executive,verificator,uploader,viewer',
            'opd_id' => 'nullable|exists:opds,id',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'role.required' => 'Role wajib dipilih.',
            'opd_id.exists' => 'OPD yang dipilih tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi.');
        }

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'opd_id' => $request->opd_id,
                'is_active' => $request->has('is_active'),
            ]);

            Log::info('User updated by admin', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id
            ]);

            return redirect()->route('users.index')
                ->with('success', 'User berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('User update failed', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal memperbarui user: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updatePassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Terjadi kesalahan validasi password.');
        }

        try {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            Log::info('User password updated by admin', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id
            ]);

            return redirect()->route('users.edit', $user)
                ->with('success', 'Password user berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('User password update failed', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal memperbarui password: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        try {
            $userName = $user->name;
            $user->delete();

            Log::info('User deleted by admin', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id
            ]);

            return redirect()->route('users.index')
                ->with('success', "User {$userName} berhasil dihapus!");

        } catch (\Exception $e) {
            Log::error('User deletion failed', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    public function toggleStatus(User $user)
    {
        // Prevent admin from deactivating themselves
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        try {
            $user->update([
                'is_active' => !$user->is_active
            ]);

            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

            Log::info('User status toggled', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'new_status' => $user->is_active
            ]);

            return redirect()->back()
                ->with('success', "Status user {$user->name} berhasil $status.");

        } catch (\Exception $e) {
            Log::error('Toggle user status failed', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal mengubah status user: ' . $e->getMessage());
        }
    }

    public function getStats(User $user)
    {
        $stats = [
            'verified_documents_count' => $user->verified_documents_count,
            'member_since' => $user->created_at->diffForHumans(),
            'last_login' => $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Never',
            'is_online' => $user->last_login_at && $user->last_login_at->gt(now()->subMinutes(15)),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function pendingVerification()
    {
        $pendingUsers = User::where('status_verifikasi', User::STATUS_PENDING)
            ->where('role', User::ROLE_VIEWER)
            ->latest()
            ->get();

        return view('users.pending-verification', compact('pendingUsers'));
    }

    public function approve(User $user)
    {
        // Hanya bisa approve user dengan status pending dan role viewer
        if ($user->status_verifikasi !== User::STATUS_PENDING || $user->role !== User::ROLE_VIEWER) {
            return redirect()->back()
                ->with('error', 'User tidak dapat disetujui.');
        }

        try {
            $user->update([
                'status_verifikasi' => User::STATUS_APPROVED,
                'is_active' => true,
                'rejection_reason' => null, // Hapus alasan penolakan jika ada
                'rejected_at' => null // Hapus tanggal penolakan
            ]);

            Log::info('User approved by admin', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id
            ]);

            return redirect()->route('users.pending-verification')
                ->with('success', "User {$user->name} berhasil disetujui!");

        } catch (\Exception $e) {
            Log::error('User approval failed', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menyetujui user: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Alasan penolakan wajib diisi.');
        }

        // Hanya bisa reject user dengan status pending dan role viewer
        if ($user->status_verifikasi !== User::STATUS_PENDING || $user->role !== User::ROLE_VIEWER) {
            return redirect()->back()
                ->with('error', 'User tidak dapat ditolak.');
        }

        try {
            $user->update([
                'status_verifikasi' => User::STATUS_REJECTED,
                'is_active' => false, // Nonaktifkan akun
                'rejection_reason' => $request->rejection_reason,
                'rejected_at' => now()
            ]);

            Log::info('User rejected by admin', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'rejection_reason' => $request->rejection_reason
            ]);

            return redirect()->route('users.pending-verification')
                ->with('success', "User {$user->name} berhasil ditolak!");

        } catch (\Exception $e) {
            Log::error('User rejection failed', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menolak user: ' . $e->getMessage());
        }
    }

    public function checkVerificationStatus()
    {
        $user = Auth::user();

        if ($user && $user->isViewer()) {
            return response()->json([
                'verified' => $user->isVerified(),
                'rejected' => $user->isRejected(),
                'status' => $user->status_verifikasi
            ]);
        }

        return response()->json(['verified' => true, 'rejected' => false]);
    }

    public function approveFromList(User $user)
    {
        if ($user->status_verifikasi !== User::STATUS_PENDING || $user->role !== User::ROLE_VIEWER) {
            return redirect()->back()
                ->with('error', 'User tidak dapat disetujui.');
        }

        try {
            $user->update([
                'status_verifikasi' => User::STATUS_APPROVED,
                'is_active' => true,
                'rejection_reason' => null,
                'rejected_at' => null
            ]);

            Log::info('User approved by admin from user list', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id
            ]);

            return redirect()->route('users.index')
                ->with('success', "User {$user->name} berhasil disetujui dan dapat mengakses sistem!");

        } catch (\Exception $e) {
            Log::error('User approval failed', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menyetujui user: ' . $e->getMessage());
        }
    }

    public function rejectFromList(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Alasan penolakan wajib diisi.');
        }

        if ($user->status_verifikasi !== User::STATUS_PENDING || $user->role !== User::ROLE_VIEWER) {
            return redirect()->back()
                ->with('error', 'User tidak dapat ditolak.');
        }

        try {
            $user->update([
                'status_verifikasi' => User::STATUS_REJECTED,
                'is_active' => false,
                'rejection_reason' => $request->rejection_reason,
                'rejected_at' => now()
            ]);

            Log::info('User rejected by admin from user list', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'rejection_reason' => $request->rejection_reason
            ]);

            return redirect()->route('users.index')
                ->with('success', "User {$user->name} berhasil ditolak!");

        } catch (\Exception $e) {
            Log::error('User rejection failed', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menolak user: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman rejection untuk user yang ditolak
     */
    public function showRejection()
    {
        $user = Auth::user();

        // Pastikan hanya user viewer yang ditolak yang bisa mengakses
        if (!$user->isViewer() || !$user->isRejected()) {
            return redirect()->route('dashboard');
        }

        return view('auth.rejection', compact('user'));
    }

    public function editVerification(User $user)
    {
        // Hanya bisa edit verifikasi untuk user viewer
        if (!$user->isViewer()) {
            return redirect()->route('users.index')
                ->with('error', 'Hanya user viewer yang memerlukan verifikasi.');
        }

        return view('users.edit-verification', compact('user'));
    }

    public function updateVerification(Request $request, User $user)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'status_verifikasi' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|max:500|required_if:status_verifikasi,rejected'
        ], [
            'status_verifikasi.required' => 'Status verifikasi wajib dipilih.',
            'status_verifikasi.in' => 'Status verifikasi tidak valid.',
            'rejection_reason.required_if' => 'Alasan penolakan wajib diisi ketika status ditolak.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi.');
        }

        // Hanya bisa update verifikasi untuk user viewer
        if (!$user->isViewer()) {
            return redirect()->route('users.index')
                ->with('error', 'Hanya user viewer yang memerlukan verifikasi.');
        }

        try {
            $oldStatus = $user->status_verifikasi;
            $newStatus = $request->status_verifikasi;
            $rejectionReason = $request->rejection_reason;

            // Update status verifikasi
            $user->updateVerificationStatus($newStatus, $rejectionReason);

            Log::info('User verification status updated by admin', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'rejection_reason' => $rejectionReason
            ]);

            $statusLabels = [
                'pending' => 'menunggu verifikasi',
                'approved' => 'disetujui',
                'rejected' => 'ditolak'
            ];

            return redirect()->route('users.show', $user)
                ->with('success', "Status verifikasi user {$user->name} berhasil diubah menjadi {$statusLabels[$newStatus]}!");

        } catch (\Exception $e) {
            Log::error('User verification update failed', [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal mengupdate status verifikasi: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function bulkApprove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Data user tidak valid.');
        }

        try {
            $approvedCount = 0;
            $userIds = $request->user_ids;

            foreach ($userIds as $userId) {
                $user = User::find($userId);

                // Hanya proses user viewer yang pending
                if ($user && $user->isViewer() && $user->isPending()) {
                    $user->updateVerificationStatus(User::STATUS_APPROVED);
                    $approvedCount++;

                    Log::info('User bulk approved by admin', [
                        'admin_id' => Auth::id(),
                        'user_id' => $user->id
                    ]);
                }
            }

            return redirect()->route('users.pending-verification')
                ->with('success', "Berhasil menyetujui {$approvedCount} user!");

        } catch (\Exception $e) {
            Log::error('Bulk approval failed', [
                'admin_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal melakukan bulk approval: ' . $e->getMessage());
        }
    }

    public function globalTimeline()
    {
        $user = Auth::user();
        $projects = Project::with([
            'developer',
            'opdRelation',
            'jenisDokumen.tahapan',
            'jenisDokumen.verifier'
        ])->latest();

        // Jika user adalah viewer dan memiliki OPD, filter berdasarkan OPD
        if ($user->isViewer() && $user->opd_id) {
            $projects->where('opd', $user->opd->name);
        }

        $projects = $projects->get();

        return view('projects.global-timeline', compact('projects'));
    }

    public function bulkStatus(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'action' => 'required|in:active,inactive'
        ]);

        $isActive = $request->action === 'active';
        $count = User::whereIn('id', $request->ids)
            ->update(['is_active' => $isActive]);

        return response()->json([
            'success' => true,
            'message' => "Berhasil " . ($isActive ? 'mengaktifkan' : 'menonaktifkan') . " {$count} pengguna."
        ]);
    }
}
