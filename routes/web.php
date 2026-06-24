<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Protected routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
      // Profile
    Route::get('user/profile', [App\Http\Controllers\Employee\ProfileController::class, 'index'])->name('user.profile.index');
    Route::put('user/profile/update', [App\Http\Controllers\Employee\ProfileController::class, 'update'])->name('user.profile.update');
});
Route::middleware(['auth', 'verified', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('employees', EmployeeController::class);
    // routes/web.php - Add this in admin group
    Route::get('designations/by-department/{departmentId}', [DesignationController::class, 'getByDepartment'])
        ->name('designations.by-department');
    // Departments
    Route::resource('departments', DepartmentController::class);

    // Designations
    Route::resource('designations', DesignationController::class);
    // ===== LEAVES =====
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\LeaveController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\LeaveController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [App\Http\Controllers\Admin\LeaveController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [App\Http\Controllers\Admin\LeaveController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [App\Http\Controllers\Admin\LeaveController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [App\Http\Controllers\Admin\LeaveController::class, 'bulkReject'])->name('bulk-reject');
    });

    // ===== ATTENDANCE =====
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\AttendanceController::class, 'show'])->name('show');
        Route::get('/employee-report/{userId?}', [App\Http\Controllers\Admin\AttendanceController::class, 'employeeReport'])
            ->name('employee-report');
        Route::get('/summary', [App\Http\Controllers\Admin\AttendanceController::class, 'summary'])->name('summary');
    });
   
});

Route::middleware(['auth', 'verified', 'role:Employee|Manager'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Employee\DashboardController::class, 'index'])->name('dashboard');
    // Attendance
    Route::get('/attendance', [App\Http\Controllers\Employee\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [App\Http\Controllers\Employee\AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [App\Http\Controllers\Employee\AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::get('/attendance/status', [App\Http\Controllers\Employee\AttendanceController::class, 'status'])->name('attendance.status');
    Route::get('/attendance/history', [App\Http\Controllers\Employee\AttendanceController::class, 'history'])->name('attendance.history');


    // Leaves
    // Leaves
    Route::get('/leaves', [App\Http\Controllers\Employee\LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [App\Http\Controllers\Employee\LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves/store', [App\Http\Controllers\Employee\LeaveController::class, 'store'])->name('leaves.store');
    Route::get('/leaves/history', [App\Http\Controllers\Employee\LeaveController::class, 'history'])->name('leaves.history');
    Route::delete('/leaves/{id}/cancel', [App\Http\Controllers\Employee\LeaveController::class, 'cancel'])->name('leaves.cancel');

  
});
Route::middleware(['auth', 'verified', 'role:Manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Manager\DashboardController::class, 'index'])->name('dashboard');
    // Team
    Route::prefix('team')->name('team.')->group(function () {
        Route::get('/', [App\Http\Controllers\Manager\TeamController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Manager\TeamController::class, 'show'])->name('show');
    });

    // Leaves
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::get('/', [App\Http\Controllers\Manager\LeaveController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Manager\LeaveController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [App\Http\Controllers\Manager\LeaveController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [App\Http\Controllers\Manager\LeaveController::class, 'reject'])->name('reject');
    });

    // Attendance
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [App\Http\Controllers\Manager\AttendanceController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Manager\AttendanceController::class, 'show'])->name('show');
        Route::get('/summary', [App\Http\Controllers\Manager\AttendanceController::class, 'summary'])->name('summary');
    });
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
