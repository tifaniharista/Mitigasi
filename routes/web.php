<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\OpdController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\TahapanController;
use App\Http\Controllers\JenisDokumenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CaptchaController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;

// Public Routes - Tidak butuh login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// CAPTCHA routes - HARUS di luar auth middleware
Route::prefix('captcha')->group(function () {
    Route::get('/generate', [CaptchaController::class, 'generate'])->name('captcha.generate');
    Route::get('/image/{id}', [CaptchaController::class, 'image'])->name('captcha.image');
    Route::post('/refresh', [CaptchaController::class, 'refresh'])->name('captcha.refresh');
    Route::post('/validate', [CaptchaController::class, 'validateCaptcha'])->name('captcha.validate');
});

// Routes yang butuh authentication
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/check-verification-status', [UserController::class, 'checkVerificationStatus'])->name('check-verification-status');

    Route::get('/waiting-verification', function () {
        $user = Auth::user();
        if (!$user->isViewer() || $user->isVerified()) {
            return redirect()->route('dashboard');
        }
        if ($user->isRejected()) {
            return redirect()->route('rejection.show');
        }
        return view('auth.waiting-verification');
    })->name('waiting-verification');

    Route::get('/rejection', [UserController::class, 'showRejection'])->name('rejection.show');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Semua route di bawah ini HANYA untuk user yang sudah terverifikasi
    Route::middleware(['verified.user'])->group(function () {
        // Profile routes
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::get('/profile/download-data', [ProfileController::class, 'downloadData'])->name('profile.download-data');

        // Routes khusus Administrator
        Route::middleware(['role:admin'])->group(function () {
            Route::resource('opds', OpdController::class);
            Route::resource('developers', DeveloperController::class);
            Route::resource('tahapans', TahapanController::class);
            Route::patch('/tahapans/{tahapan}/toggle-status', [TahapanController::class, 'toggleStatus'])->name('tahapans.toggle-status');
            Route::post('/tahapans/update-order', [TahapanController::class, 'updateOrder'])->name('tahapans.update-order');
            Route::patch('/users/{user}/toggle-status', [ProfileController::class, 'toggleStatus'])->name('users.toggle-status');
        });

        // Routes untuk Projects
        Route::prefix('projects')->group(function () {
            Route::middleware(['role:admin,uploader'])->group(function () {
                Route::get('/create', [ProjectController::class, 'create'])->name('projects.create');
                Route::post('/', [ProjectController::class, 'store'])->name('projects.store');
                Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
                Route::patch('/{project}', [ProjectController::class, 'update'])->name('projects.update');
                Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
            });

            Route::get('/', [ProjectController::class, 'index'])->name('projects.index')->middleware('role:admin,executive,verificator,uploader,viewer');
            Route::get('/{project}', [ProjectController::class, 'show'])->name('projects.show')->middleware('role:admin,executive,verificator,uploader,viewer');
            Route::get('/{project}/timeline', [ProjectController::class, 'timeline'])->name('projects.timeline')->middleware('role:admin,executive,verificator,uploader,viewer');
            Route::get('/timeline/global', [ProjectController::class, 'globalTimeline'])->name('projects.timeline.global')->middleware('role:admin,executive,verificator,uploader,viewer');

            // Routes export HANYA untuk admin
            Route::middleware(['role:admin'])->group(function () {
                Route::get('/export/excel', [ProjectController::class, 'exportExcel'])->name('projects.export.excel');
                Route::get('/export/pdf', [ProjectController::class, 'exportPdf'])->name('projects.export.pdf');
            });

            // Routes untuk project extension dan completion
            Route::middleware(['role:admin,uploader'])->group(function () {
                Route::get('/projects/{project}/extend', [ProjectController::class, 'showExtensionForm'])->name('projects.extend-form');
                Route::post('/projects/{project}/extend', [ProjectController::class, 'extend'])->name('projects.extend');
                Route::post('/projects/{project}/complete', [ProjectController::class, 'markAsCompleted'])->name('projects.complete');
            });

            // Routes untuk menutup project (hanya admin)
            Route::middleware(['role:admin'])->group(function () {
                Route::get('/{project}/close', [ProjectController::class, 'showClosureForm'])->name('projects.close.form');
                Route::post('/{project}/close', [ProjectController::class, 'closeProject'])->name('projects.close');
                Route::post('/{project}/reopen', [ProjectController::class, 'reopenProject'])->name('projects.reopen');
            });
        });

        // Routes untuk Jenis Dokumen
        Route::prefix('jenis-dokumen')->group(function () {
            Route::middleware(['role:admin,uploader'])->group(function () {
                Route::get('/create', [JenisDokumenController::class, 'create'])->name('jenis-dokumen.create');
                Route::post('/', [JenisDokumenController::class, 'store'])->name('jenis-dokumen.store');
                Route::get('/{jenisDokuman}/edit', [JenisDokumenController::class, 'edit'])->name('jenis-dokumen.edit');
                Route::put('/{jenisDokuman}', [JenisDokumenController::class, 'update'])->name('jenis-dokumen.update');
                Route::delete('/{jenisDokuman}', [JenisDokumenController::class, 'destroy'])->name('jenis-dokumen.destroy');
                Route::post('/bulk-status', [JenisDokumenController::class, 'bulkStatus'])->name('jenis-dokumen.bulk-status');
            });

            Route::get('/', [JenisDokumenController::class, 'index'])->name('jenis-dokumen.index')->middleware('role:admin,executive,verificator,uploader,viewer');
            Route::get('/{jenisDokuman}', [JenisDokumenController::class, 'show'])->name('jenis-dokumen.show')->middleware('role:admin,executive,verificator,uploader,viewer');
            Route::get('/{jenisDokuman}/view-dokumen', [JenisDokumenController::class, 'viewDokumen'])->name('jenis-dokumen.view-dokumen')->middleware('role:admin,executive,verificator,uploader,viewer');
            Route::get('/{jenisDokuman}/view-pendukung', [JenisDokumenController::class, 'viewPendukung'])->name('jenis-dokumen.view-pendukung')->middleware('role:admin,executive,verificator,uploader,viewer');
            Route::get('/{jenisDokuman}/download-dokumen', [JenisDokumenController::class, 'downloadDokumen'])->name('jenis-dokumen.download-dokumen')->middleware('role:admin,executive,verificator,uploader,viewer');
            Route::get('/{jenisDokuman}/download-pendukung', [JenisDokumenController::class, 'downloadPendukung'])->name('jenis-dokumen.download-pendukung')->middleware('role:admin,executive,verificator,uploader,viewer');
            Route::patch('/{jenisDokuman}/verify', [JenisDokumenController::class, 'verify'])->name('jenis-dokumen.verify')->middleware('role:admin,verificator');
            Route::patch('/{jenisDokuman}/toggle-status', [JenisDokumenController::class, 'toggleStatus'])->name('jenis-dokumen.toggle-status')->middleware('role:admin,uploader');
        });

        Route::get('projects/{project}/dokumen', [JenisDokumenController::class, 'byProject'])->name('projects.dokumen')->middleware('role:admin,executive,verificator,uploader,viewer');

        // User management hanya untuk admin
        Route::middleware(['role:admin'])->prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('users.index');
            Route::get('/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/', [UserController::class, 'store'])->name('users.store');
            Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::patch('/{user}', [UserController::class, 'update'])->name('users.update');
            Route::patch('/{user}/password', [UserController::class, 'updatePassword'])->name('users.update-password');
            Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
            Route::get('/{user}/stats', [UserController::class, 'getStats'])->name('users.stats');

            // Routes untuk verifikasi user viewer
            Route::get('/pending-verification', [UserController::class, 'pendingVerification'])->name('users.pending-verification');
            Route::patch('/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
            Route::patch('/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
            Route::patch('/{user}/approve-from-list', [UserController::class, 'approveFromList'])->name('users.approve-from-list');
            Route::patch('/{user}/reject-from-list', [UserController::class, 'rejectFromList'])->name('users.reject-from-list');
            Route::get('/{user}/edit-verification', [UserController::class, 'editVerification'])->name('users.edit-verification');
            Route::patch('/{user}/update-verification', [UserController::class, 'updateVerification'])->name('users.update-verification');
            Route::post('/bulk-approve', [UserController::class, 'bulkApprove'])->name('users.bulk-approve');
            Route::post('/bulk-status', [UserController::class, 'bulkStatus'])->name('users.bulk-status');
        });
    });
});
