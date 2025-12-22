<?php

use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CustomerContactController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
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
            Route::get('plan', [ProjectKickoffController::class, 'plan'])->name('plan');
            Route::post('plan', [ProjectKickoffController::class, 'storePlan'])->name('plan.store');
            Route::get('schedule', [ProjectKickoffController::class, 'schedule'])->name('schedule');
            Route::put('schedule', [ProjectKickoffController::class, 'updateSchedule'])->name('schedule.update');
            Route::get('complete', [ProjectKickoffController::class, 'complete'])->name('complete');
            Route::put('complete', [ProjectKickoffController::class, 'updateComplete'])->name('complete.update');
            Route::delete('/', [ProjectKickoffController::class, 'destroy'])->name('destroy');
        });
        Route::prefix('projects/{project}/requirements')->name('projects.requirements.')->group(function () {
            Route::get('import', [ProjectRequirementController::class, 'import'])->name('import');
            Route::post('import/preview', [ProjectRequirementController::class, 'previewImport'])->name('import.preview');
            Route::post('import', [ProjectRequirementController::class, 'storeImport'])->name('import.store');
            Route::post('rfp', [ProjectRequirementController::class, 'storeRfp'])->name('rfp.store');
            Route::get('rfp/{rfp_document}', [ProjectRequirementController::class, 'downloadRfp'])->name('rfp.download');
        });
        Route::resource('projects.requirements', ProjectRequirementController::class)->except(['show']);
        Route::prefix('projects/{project}/drive-documents')->name('projects.drive-documents.')->group(function () {
            Route::get('/', [DocumentController::class, 'index'])->name('index');
            Route::get('folders/{drive_folder}', [DocumentController::class, 'show'])->name('folders.show');
            Route::post('folders', [DocumentController::class, 'storeFolder'])->name('folders.store');
            Route::post('upload', [DocumentController::class, 'store'])->name('upload');
            Route::patch('{drive_document}/rename', [DocumentController::class, 'rename'])->name('rename');
            Route::patch('{drive_document}/move', [DocumentController::class, 'move'])->name('move');
            Route::post('{drive_document}/copy', [DocumentController::class, 'copy'])->name('copy');
            Route::delete('{drive_document}', [DocumentController::class, 'destroy'])->name('destroy');
        });
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
