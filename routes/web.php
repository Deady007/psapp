<?php

use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CustomerContactController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectDocumentController;
use App\Http\Controllers\ProjectKickoffController;
use App\Http\Controllers\ProjectRequirementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin|user'])
    ->scopeBindings()
    ->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::resource('customers.contacts', CustomerContactController::class);
        Route::resource('projects', ProjectController::class);
        Route::prefix('projects/{project}/kickoff')->name('projects.kickoffs.')->group(function () {
            Route::get('/', [ProjectKickoffController::class, 'show'])->name('show');
            Route::get('create', [ProjectKickoffController::class, 'create'])->name('create');
            Route::post('/', [ProjectKickoffController::class, 'store'])->name('store');
            Route::get('edit', [ProjectKickoffController::class, 'edit'])->name('edit');
            Route::put('/', [ProjectKickoffController::class, 'update'])->name('update');
            Route::delete('/', [ProjectKickoffController::class, 'destroy'])->name('destroy');
        });
        Route::resource('projects.requirements', ProjectRequirementController::class)->except(['show']);
        Route::resource('projects.documents', ProjectDocumentController::class)->except(['show']);
        Route::get('projects/{project}/documents/{document}/download', [ProjectDocumentController::class, 'download'])
            ->name('projects.documents.download');
    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('users', AdminUserController::class);
        Route::resource('roles', AdminRoleController::class);
        Route::resource('permissions', AdminPermissionController::class);
    });

require __DIR__.'/auth.php';
