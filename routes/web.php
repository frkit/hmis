<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DoctorController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// User Management (admin only — enforced in controller)
Route::resource('users', UserController::class);
Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

// Task Management
Route::resource('tasks', TaskController::class);
Route::post('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');

// Inventory
Route::resource('inventory', InventoryController::class);
Route::post('inventory/{inventory}/transaction', [InventoryController::class, 'transaction'])->name('inventory.transaction');

// Departments (Clinical)
Route::prefix('clinical')->group(function() {
    Route::get('departments', [DoctorController::class, 'departments'])->name('doctors.departments');
    Route::post('departments', [DoctorController::class, 'storeDepartment'])->name('departments.store');
    Route::put('departments/{department}', [DoctorController::class, 'updateDepartment'])->name('departments.update');
    Route::delete('departments/{department}', [DoctorController::class, 'destroyDepartment'])->name('departments.destroy');
});
