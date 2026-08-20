<?php

use App\Http\Controllers\AccessRequestController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\PermitTypeController;
use App\Http\Controllers\Admin\RequirementController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WebsiteController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentFolderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SettingsController;
use Illuminate\Auth\Requests\EmailVerificationRequest;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Route;

// Landing page (publik)
Route::get('/landing', fn () => view('landing'))->name('landing');

// Root
Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'landing'));

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

    // Gambar captcha untuk form login (kode disimpan sebagai hash di session).
    Route::get('/captcha', \App\Http\Controllers\Auth\CaptchaController::class.'@show')->name('captcha');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ---------------- Verifikasi Email ----------------
    // Tautan dari email (signed URL, berlaku sesuai config/verification.php).
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('dashboard')
            ->with('success', 'Email Anda berhasil diverifikasi. Terima kasih!');
    })->middleware('signed')->name('verification.verify');

    // Kirim ulang email verifikasi (rate-limited 6x/menit).
    Route::post('/email/verification-notification', function (HttpRequest $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('info', 'Email Anda sudah terverifikasi.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Tautan verifikasi telah dikirim ulang ke email Anda.');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // ---------------- Pengajuan (Perizinan & Agreement) ----------------
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/create', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
    Route::get('/applications/{application}/edit', [ApplicationController::class, 'edit'])->name('applications.edit');
    Route::match(['put', 'patch'], '/applications/{application}', [ApplicationController::class, 'update'])->name('applications.update');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/submit', [ApplicationController::class, 'submit'])->name('applications.submit');

    // ---------------- Dokumen ----------------
    Route::get('/documents/browse', [DocumentController::class, 'browse'])->name('documents.browse');
    Route::get('/documents/folders', [DocumentFolderController::class, 'index'])->name('documents.folders');
    Route::post('/documents/folders', [DocumentFolderController::class, 'store'])->name('documents.folders.store');
    Route::put('/documents/folders/{folder}', [DocumentFolderController::class, 'update'])->name('documents.folders.update');
    Route::delete('/documents/folders/{folder}', [DocumentFolderController::class, 'destroy'])->name('documents.folders.destroy');
    Route::post('/applications/{application}/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/applications/{application}/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/documents/download/{document}', [DocumentController::class, 'download'])->name('documents.download');

    // ---------------- Permintaan Akses Dokumen ----------------
    Route::post('/applications/{application}/access-requests', [AccessRequestController::class, 'store'])
        ->name('access-requests.store');
    Route::get('/access-requests/mine', [AccessRequestController::class, 'mine'])->name('access-requests.mine');
    Route::middleware('role:legal,admin')->group(function () {
        Route::get('/access-requests/incoming', [AccessRequestController::class, 'incoming'])
            ->name('access-requests.incoming');
        Route::post('/access-requests/{access_request}/approve', [AccessRequestController::class, 'approve'])
            ->name('access-requests.approve');
        Route::post('/access-requests/{access_request}/reject', [AccessRequestController::class, 'reject'])
            ->name('access-requests.reject');
    });

    // ---------------- Review Legal/Admin ----------------
    Route::middleware('role:legal,admin')->group(function () {
        Route::get('/review-queue', [ReviewController::class, 'queue'])->name('review.queue');
        Route::post('/applications/{application}/review/start', [ReviewController::class, 'start'])
            ->name('review.start');
        Route::post('/applications/{application}/review/decide', [ReviewController::class, 'decide'])
            ->name('review.decide');
    });

    // ---------------- Notifikasi ----------------
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])
        ->name('notifications.read-all');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'read'])
        ->name('notifications.read');

    // ---------------- Pengaturan ----------------
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');

    Route::middleware('role:admin')->group(function () {
        Route::post('/settings/company', [SettingsController::class, 'updateCompany'])->name('settings.company.update');
        Route::post('/settings/smtp', [SettingsController::class, 'updateSmtp'])->name('settings.smtp.update');
        Route::post('/settings/smtp/test', [SettingsController::class, 'sendTestEmail'])->name('settings.smtp.test');
    });

    // ---------------- Area Admin ----------------
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('permit-types', PermitTypeController::class)->except(['show']);
        Route::resource('suppliers', SupplierController::class)->except(['show']);

        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::get('/requirements', [RequirementController::class, 'index'])->name('requirements.index');
        Route::post('/requirements', [RequirementController::class, 'store'])->name('requirements.store');
        Route::put('/requirements/{requirement}', [RequirementController::class, 'update'])->name('requirements.update');
        Route::delete('/requirements/{requirement}', [RequirementController::class, 'destroy'])->name('requirements.destroy');

        Route::get('/histories', [HistoryController::class, 'index'])->name('histories.index');
    });

    // ---------------- Kelola Website (khusus Super Admin) ----------------
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/website', [WebsiteController::class, 'index'])->name('website.index');
        Route::post('/website', [WebsiteController::class, 'update'])->name('website.update');
        Route::post('/website/reset', [WebsiteController::class, 'reset'])->name('website.reset');
    });
});
