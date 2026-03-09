<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\DisposalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware(['auth', 'check.user.active'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Equipment
    Route::get('/equipment/export', [EquipmentController::class, 'export'])->name('equipment.export');
    Route::resource('equipment', EquipmentController::class);
    
    // Transactions
    Route::get('/transactions/incoming', [TransactionController::class, 'incoming'])->name('transactions.incoming');
    Route::get('/transactions/outgoing', [TransactionController::class, 'outgoing'])->name('transactions.outgoing');
    Route::get('/transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
    Route::patch('/transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
    Route::patch('/transactions/{transaction}/complete', [TransactionController::class, 'complete'])->name('transactions.complete');
    Route::resource('transactions', TransactionController::class);
    
    // Borrowings
    Route::get('/borrowings/overdue', [BorrowingController::class, 'overdue'])->name('borrowings.overdue');
    Route::get('/borrowings/export', [BorrowingController::class, 'export'])->name('borrowings.export');
    Route::post('/borrowings/{borrowing}/return', [BorrowingController::class, 'processReturn'])->name('borrowings.return');
    Route::resource('borrowings', BorrowingController::class);
    
    // Maintenances
    Route::get('/maintenances/scheduled', [MaintenanceController::class, 'scheduled'])->name('maintenances.scheduled');
    Route::get('/maintenances/export', [MaintenanceController::class, 'export'])->name('maintenances.export');
    Route::patch('/maintenances/{maintenance}/start', [MaintenanceController::class, 'start'])->name('maintenances.start');
    Route::patch('/maintenances/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('maintenances.complete');
    Route::resource('maintenances', MaintenanceController::class);
    
    // Disposals
    Route::get('/disposals/export', [DisposalController::class, 'export'])->name('disposals.export');
    Route::get('/disposals/bulk-create', [DisposalController::class, 'bulkCreate'])->name('disposals.bulk-create');
    Route::post('/disposals/bulk-store', [DisposalController::class, 'bulkStore'])->name('disposals.bulk-store');
    Route::patch('/disposals/{disposal}/approve', [DisposalController::class, 'approve'])->name('disposals.approve');
    Route::patch('/disposals/{disposal}/complete', [DisposalController::class, 'complete'])->name('disposals.complete');
    Route::delete('/disposals/{disposal}/delete-equipment', [DisposalController::class, 'deleteEquipment'])->name('disposals.delete-equipment');
    Route::resource('disposals', DisposalController::class);
    
    // Users Management
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', UserController::class);
    
    // Master Data
    Route::resource('brands', BrandController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('locations', LocationController::class);
    
    // Audit Trail
    Route::get('/audit-trail', [AuditTrailController::class, 'index'])->name('audit-trail.index');
    Route::get('/audit-trail/{activity}', [AuditTrailController::class, 'show'])->name('audit-trail.show');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/equipment', [ReportController::class, 'equipment'])->name('reports.equipment');
    Route::get('/reports/transactions', [ReportController::class, 'transactions'])->name('reports.transactions');
    Route::get('/reports/borrowings', [ReportController::class, 'borrowings'])->name('reports.borrowings');
    Route::get('/reports/maintenances', [ReportController::class, 'maintenances'])->name('reports.maintenances');
    Route::get('/reports/disposals', [ReportController::class, 'disposals'])->name('reports.disposals');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
    
    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
});
