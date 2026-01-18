<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Public pricing page
Route::get('/pricing', [App\Http\Controllers\PricingController::class, 'index'])->name('pricing');

// Public tenant registration
Route::get('/register', [App\Http\Controllers\TenantRegistrationController::class, 'index'])->name('tenant.register');
Route::post('/register', [App\Http\Controllers\TenantRegistrationController::class, 'store'])->name('tenant.register.store');
Route::get('/verify-email/{token}', [App\Http\Controllers\TenantRegistrationController::class, 'verifyEmail'])->name('tenant.verify-email');
Route::post('/resend-verification', [App\Http\Controllers\TenantRegistrationController::class, 'resendVerification'])->name('tenant.resend-verification');
Route::get('/register/success', function () {
    return \Inertia\Inertia::render('TenantRegistration/Success');
})->name('tenant.register.success');

// Checkout (requires auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout/{plan}', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/process', [App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [App\Http\Controllers\CheckoutController::class, 'cancel'])->name('checkout.cancel');
    
    // Subscription Management
    Route::get('/subscriptions/manage', [App\Http\Controllers\SubscriptionController::class, 'manage'])->name('subscriptions.manage');
    Route::post('/subscriptions/upgrade', [App\Http\Controllers\SubscriptionController::class, 'upgrade'])->name('subscriptions.upgrade');
    Route::post('/subscriptions/cancel', [App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
});

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Tenant Management
    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\TenantController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\TenantController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\TenantController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Admin\TenantController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\TenantController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\TenantController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Admin\TenantController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/suspend', [App\Http\Controllers\Admin\TenantController::class, 'suspend'])->name('suspend');
        Route::post('/{id}/activate', [App\Http\Controllers\Admin\TenantController::class, 'activate'])->name('activate');
    });
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('show');
        Route::post('/{id}/suspend', [App\Http\Controllers\Admin\UserController::class, 'suspend'])->name('suspend');
        Route::post('/{id}/reset-password', [App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('reset-password');
        Route::post('/{id}/impersonate', [App\Http\Controllers\Admin\UserController::class, 'impersonate'])->name('impersonate');
    });
    
    // System Monitoring
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SystemController::class, 'index'])->name('index');
        Route::get('/health', [App\Http\Controllers\Admin\SystemController::class, 'health'])->name('health');
        Route::get('/metrics', [App\Http\Controllers\Admin\SystemController::class, 'metrics'])->name('metrics');
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
        Route::put('/', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('update');
        Route::post('/test-connection', [App\Http\Controllers\Admin\SettingsController::class, 'testConnection'])->name('test-connection');
    });
    
    // Plans Management
    Route::prefix('plans')->name('plans.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PlanController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\PlanController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\PlanController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Admin\PlanController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\PlanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\PlanController::class, 'update'])->name('update');
    });
    
    // Subscriptions Management
    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\SubscriptionController::class, 'show'])->name('show');
    });
    
    // Manual Payment Approval
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/manual', [App\Http\Controllers\Admin\ManualPaymentController::class, 'index'])->name('manual.index');
        Route::get('/manual/{id}', [App\Http\Controllers\Admin\ManualPaymentController::class, 'show'])->name('manual.show');
        Route::post('/manual/{id}/approve', [App\Http\Controllers\Admin\ManualPaymentController::class, 'approve'])->name('manual.approve');
        Route::post('/manual/{id}/reject', [App\Http\Controllers\Admin\ManualPaymentController::class, 'reject'])->name('manual.reject');
    });
    
    // Feature Flags Management
    Route::prefix('feature-flags')->name('feature-flags.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\FeatureFlagController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\FeatureFlagController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\FeatureFlagController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Admin\FeatureFlagController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\FeatureFlagController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\FeatureFlagController::class, 'update'])->name('update');
    });
    
    // Audit Logs & Security
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\AuditLogController::class, 'show'])->name('show');
    });
});
