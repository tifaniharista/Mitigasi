<?php
// [file name]: ProfileController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Opd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman edit profile
     */
    public function edit()
    {
        $user = Auth::user();
        $opds = Opd::active()->get();

        // Load relationships untuk recent activity
        $user->load(['verifiedDocuments' => function($query) {
            $query->latest()->take(5);
        }]);

        // Tentukan apakah OPD harus ditampilkan
        $showOpdField = $this->shouldShowOpdField($user);

        return view('profile.edit', compact('user', 'opds', 'showOpdField'));
    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Log request untuk debugging
        Log::info('Profile update request received', [
            'user_id' => $user->id,
            'request_data' => $request->all()
        ]);

        // Tentukan apakah OPD harus divalidasi
        $showOpdField = $this->shouldShowOpdField($user);

        // Validasi data
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ];

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
        ];

        // Hanya tambahkan validasi OPD jika field ditampilkan
        if ($showOpdField) {
            $rules['opd_id'] = 'required|exists:opds,id';
            $messages['opd_id.required'] = 'OPD wajib dipilih.';
            $messages['opd_id.exists'] = 'OPD yang dipilih tidak valid.';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        // Jika validasi gagal
        if ($validator->fails()) {
            Log::warning('Profile validation failed', [
                'user_id' => $user->id,
                'errors' => $validator->errors()->toArray()
            ]);

            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi. Silakan periksa form Anda.');
        }

        try {
            // Update data user
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            // Hanya update OPD jika field ditampilkan dan ada di request
            if ($showOpdField && $request->has('opd_id')) {
                $updateData['opd_id'] = $request->opd_id;
            }

            $user->update($updateData);

            Log::info('Profile updated successfully', [
                'user_id' => $user->id,
                'updated_fields' => $updateData
            ]);

            return redirect()->route('profile.edit')
                ->with('success', 'Profile berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function shouldShowOpdField(User $user)
    {
        // Admin, Uploader, Executive, Verificator selalu bisa melihat dan mengubah OPD
        if ($user->isAdmin() || $user->isUploader() || $user->isExecutive() || $user->isVerificator()) {
            return true;
        }

        // Viewer hanya bisa melihat/mengubah OPD jika:
        // 1. Belum memiliki OPD sama sekali (null)
        // 2. Atau belum pernah login sebelumnya (first login)
        if ($user->isViewer()) {
            // Jika OPD masih null, tampilkan
            if (is_null($user->opd_id)) {
                return true;
            }

            // Jika belum pernah login sebelumnya, tampilkan
            if (is_null($user->last_login_at)) {
                return true;
            }

            // Sudah punya OPD dan sudah pernah login, sembunyikan
            return false;
        }

        // Default untuk role lainnya
        return true;
    }

    /**
     * Update password user
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        Log::info('Password update request received', [
            'user_id' => $user->id
        ]);

        // Validasi password
        $validator = Validator::make($request->all(), [
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('Password saat ini tidak valid.');
                    }
                },
            ],
            'new_password' => 'required|min:8|confirmed|different:current_password',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
            'new_password.different' => 'Password baru harus berbeda dengan password saat ini.',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            Log::warning('Password validation failed', [
                'user_id' => $user->id,
                'errors' => $validator->errors()->toArray()
            ]);

            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi password.');
        }

        try {
            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            Log::info('Password updated successfully', [
                'user_id' => $user->id
            ]);

            return redirect()->route('profile.edit')
                ->with('success', 'Password berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Password update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get user statistics (API endpoint jika diperlukan)
     */
    public function getStats()
    {
        $user = Auth::user();

        $stats = [
            'verified_documents_count' => $user->verified_documents_count,
            'member_since' => $user->created_at->diffForHumans(),
            'last_login' => $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Never',
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Update last login time (bisa dipanggil dari LoginController)
     */
    public function updateLastLogin()
    {
        $user = Auth::user();

        if ($user) {
            $user->update(['last_login_at' => now()]);

            Log::info('Last login updated', [
                'user_id' => $user->id,
                'last_login_at' => now()
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle user active status (hanya untuk admin)
     */
    public function toggleStatus(Request $request, User $user)
    {
        // Hanya admin yang bisa mengakses
        if (!Auth::user()->isAdmin()) {
            Log::warning('Unauthorized access to toggle user status', [
                'attempted_by' => Auth::id(),
                'target_user' => $user->id
            ]);

            return redirect()->back()
                ->with('error', 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }

        try {
            $user->update([
                'is_active' => !$user->is_active
            ]);

            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

            Log::info('User status toggled', [
                'admin_id' => Auth::id(),
                'target_user' => $user->id,
                'new_status' => $user->is_active
            ]);

            return redirect()->back()
                ->with('success', "Status user {$user->name} berhasil $status.");

        } catch (\Exception $e) {
            Log::error('Toggle user status failed', [
                'admin_id' => Auth::id(),
                'target_user' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal mengubah status user: ' . $e->getMessage());
        }
    }

    /**
     * Download user data (GDPR compliance)
     */
    public function downloadData()
    {
        $user = Auth::user();

        try {
            $userData = [
                'profile' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'opd' => $user->opd ? $user->opd->name : 'Tidak ada',
                    'created_at' => $user->created_at->toISOString(),
                    'last_login_at' => $user->last_login_at ? $user->last_login_at->toISOString() : null,
                ],
                'activity' => [
                    'verified_documents_count' => $user->verified_documents_count,
                ]
            ];

            $filename = "user_data_{$user->id}_" . now()->format('Y-m-d_H-i-s') . '.json';

            Log::info('User data download requested', [
                'user_id' => $user->id
            ]);

            return response()->json($userData)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', "attachment; filename=$filename");

        } catch (\Exception $e) {
            Log::error('User data download failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal mengunduh data: ' . $e->getMessage());
        }
    }
}
