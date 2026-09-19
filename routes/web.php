<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\CommentReactionController;
use App\Http\Controllers\TaskAttachmentController;
use App\Http\Controllers\TaskPaymentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProjectAssetController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ContentApprovalController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\Frontend\FrontendController;
use Illuminate\Support\Facades\Route;


use Illuminate\Support\Facades\Artisan;

Route::get('/run-link', function () {
    Artisan::call('storage:link');
    return 'Storage link created successfully!';
});

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-and-conditions', [FrontendController::class, 'termsAndConditions'])->name('terms-and-conditions');
Route::get('/robots.txt', [FrontendController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [FrontendController::class, 'sitemap'])->name('sitemap');
Route::get('/llms.txt', [FrontendController::class, 'llmsTxt'])->name('llms-txt');

// Public invitation routes
Route::get('/invitation/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');
Route::post('/invitation/{token}', [InvitationController::class, 'register'])->name('invitations.register');

Route::middleware(['auth', 'verified'])->group(function () {

    // All authenticated users
    Route::resource('tasks', TaskController::class);

    // Projects — Admin + Project Manager only
    Route::middleware('pm_or_admin')->group(function () {
        Route::resource('projects', ProjectController::class);

        // Project Media Library
        Route::get('/projects/{project}/media', [MediaController::class, 'index'])->name('projects.media');
        Route::post('/projects/{project}/media', [MediaController::class, 'store'])->name('projects.media.store');
        Route::get('/projects/{project}/media/{media}/download', [MediaController::class, 'download'])->name('projects.media.download');
        Route::delete('/projects/{project}/media/{media}', [MediaController::class, 'destroy'])->name('projects.media.destroy');

        // Project Assets (legacy)
        Route::get('/projects/{project}/assets', [ProjectAssetController::class, 'index'])->name('projects.assets');
        Route::post('/projects/{project}/assets', [ProjectAssetController::class, 'store'])->name('projects.assets.store');
        Route::delete('/projects/{project}/assets/{asset}', [ProjectAssetController::class, 'destroy'])->name('projects.assets.destroy');
    });

    // Share task content with client for approval — Super Admin + Project Manager only
    Route::post('/tasks/{task}/share-with-client', [TaskController::class, 'shareWithClient'])->name('tasks.share-with-client');

    // Task comments
    Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tasks.comments.store');
    Route::delete('/tasks/{task}/comments/{comment}', [TaskCommentController::class, 'destroy'])->name('tasks.comments.destroy');
    Route::post('/tasks/{task}/comments/{comment}/reactions', [CommentReactionController::class, 'toggle'])->name('tasks.comments.reactions.toggle');

    // Task attachments
    Route::post('/tasks/{task}/attachments', [TaskAttachmentController::class, 'store'])->name('tasks.attachments.store');
    Route::get('/tasks/{task}/attachments/{media}/download', [TaskAttachmentController::class, 'download'])->name('tasks.attachments.download');
    Route::delete('/tasks/{task}/attachments/{media}', [TaskAttachmentController::class, 'destroy'])->name('tasks.attachments.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    Route::post('/profile/notifications', [ProfileController::class, 'updateNotificationPreferences'])->name('profile.notifications.update');

    // Calendar (admin only via middleware below, but events endpoint needs auth)
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    // Client portal
    Route::middleware('client')->group(function () {
        Route::get('/portal', fn () => view('client.dashboard'))->name('client.dashboard');
        Route::get('/portal/notifications', [NotificationController::class, 'clientIndex'])->name('client.notifications');
        Route::get('/portal/reports', [ReportController::class, 'clientIndex'])->name('client.reports.index');
        Route::get('/portal/reports/{report}', [ReportController::class, 'clientShow'])->name('client.reports.show');

        Route::get('/portal/content', [ContentApprovalController::class, 'index'])->name('client.content.index');
        Route::get('/portal/content/{task}', [ContentApprovalController::class, 'show'])->name('client.content.show');
        Route::post('/portal/content/{task}/approve', [ContentApprovalController::class, 'approve'])->name('client.content.approve');
        Route::post('/portal/content/{task}/request-revision', [ContentApprovalController::class, 'requestRevision'])->name('client.content.request-revision');
    });

    // Super Admin + Project Manager routes
    Route::middleware('pm_or_admin')->group(function () {
        Route::resource('clients', ClientController::class);
        Route::resource('invoices', InvoiceController::class);
        Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

        Route::resource('reports', ReportController::class);
        Route::post('/reports/{report}/send', [ReportController::class, 'send'])->name('reports.send');
        Route::get('/reports/{report}/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
    });

    // Admin-only routes
    Route::middleware('admin')->group(function () {
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/team', [TeamController::class, 'index'])->name('team.index');
        Route::get('/team/{user}', [TeamController::class, 'show'])->name('team.show');
        Route::delete('/team/{user}', [TeamController::class, 'destroy'])->name('team.destroy');
        Route::post('/team/{user}/reset-link', [TeamController::class, 'generateResetLink'])->name('team.reset-link');
        Route::post('/team/{user}/roles', [TeamController::class, 'updateRoles'])->name('team.update-roles');
        Route::post('/team/{user}/profile', [MemberProfileController::class, 'update'])->name('team.profile.update');
        Route::post('/team/{user}/profile/documents', [MemberProfileController::class, 'uploadDocument'])->name('team.profile.documents.store');
        Route::get('/team/{user}/profile/documents/{media}/download', [MemberProfileController::class, 'downloadDocument'])->name('team.profile.documents.download');
        Route::delete('/team/{user}/profile/documents/{media}', [MemberProfileController::class, 'destroyDocument'])->name('team.profile.documents.destroy');
        Route::get('/invitations', [InvitationController::class, 'index'])->name('invitations.index');
        Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
        Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
        Route::delete('/invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');

        // Roles & Permissions
        Route::resource('roles', RoleController::class)->except('show');

        // Salaries
        Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries.index');
        Route::get('/salaries/{user}', [SalaryController::class, 'show'])->name('salaries.show');
        Route::post('/salaries/{user}', [SalaryController::class, 'store'])->name('salaries.store');
        Route::delete('/salaries/{record}', [SalaryController::class, 'destroy'])->name('salaries.destroy');
        Route::post('/salaries/{user}/payments', [SalaryController::class, 'storePayment'])->name('salaries.payments.store');
        Route::delete('/salaries/payments/{payment}', [SalaryController::class, 'destroyPayment'])->name('salaries.payments.destroy');

        // Task-based payments (pay assignees/freelancers for completed tasks)
        Route::post('/tasks/{task}/payments', [TaskPaymentController::class, 'store'])->name('tasks.payments.store');
        Route::delete('/task-payments/{payment}', [TaskPaymentController::class, 'destroy'])->name('tasks.payments.destroy');

        // Finance (investor payments + expenses)
        Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('/finance/payments', [FinanceController::class, 'storePayment'])->name('finance.payments.store');
        Route::delete('/finance/payments/{payment}', [FinanceController::class, 'destroyPayment'])->name('finance.payments.destroy');
        Route::post('/finance/expenses', [FinanceController::class, 'storeExpense'])->name('finance.expenses.store');
        Route::delete('/finance/expenses/{expense}', [FinanceController::class, 'destroyExpense'])->name('finance.expenses.destroy');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/logo', [SettingsController::class, 'updateLogo'])->name('settings.logo.update');
        Route::delete('/settings/logo', [SettingsController::class, 'destroyLogo'])->name('settings.logo.destroy');
        Route::post('/settings/cache/clear', [SettingsController::class, 'clearCache'])->name('settings.cache.clear');
        Route::post('/settings/backups/run', [SettingsController::class, 'runBackup'])->name('settings.backups.run');
        Route::get('/settings/backups/{disk}/{filename}/download', [SettingsController::class, 'downloadBackup'])->name('settings.backups.download');
        Route::delete('/settings/backups/{disk}/{filename}', [SettingsController::class, 'destroyBackup'])->name('settings.backups.destroy');
    });
});

require __DIR__.'/auth.php';
