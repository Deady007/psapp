<?php

use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CustomerContactController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Kanban\KanbanBoardController;
use App\Http\Controllers\Kanban\KanbanBoardDocumentController;
use App\Http\Controllers\Kanban\KanbanBugController;
use App\Http\Controllers\Kanban\KanbanStoryController;
use App\Http\Controllers\Kanban\KanbanTestingCardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectKickoffController;
use App\Http\Controllers\ProjectRequirementController;
use App\Http\Controllers\SettingsController;
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

Route::middleware(['auth', 'role:admin|user|developer|tester'])
    ->scopeBindings()
    ->group(function () {
        Route::get('/settings/application', [SettingsController::class, 'application'])->name('settings.application');
        Route::get('/settings', function () {
            return redirect()->to(route('settings.application').'#ui-theme');
        })->name('settings');

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
        Route::prefix('projects/{project}/kanban')->name('projects.kanban.')->group(function () {
            Route::get('/', [KanbanBoardController::class, 'index'])->name('index');
            Route::get('boards/{board}', [KanbanBoardController::class, 'show'])->name('boards.show');
            Route::post('boards/{board}/stories', [KanbanStoryController::class, 'store'])->name('boards.stories.store');
            Route::post('boards/{board}/stories/{story}/assign', [KanbanStoryController::class, 'assign'])->name('boards.stories.assign');
            Route::patch('boards/{board}/stories/{story}', [KanbanStoryController::class, 'update'])->name('boards.stories.update');
            Route::post('boards/{board}/stories/{story}/move', [KanbanStoryController::class, 'move'])->name('boards.stories.move');
            Route::post('boards/{board}/stories/{story}/send-to-testing', [KanbanStoryController::class, 'sendToTesting'])
                ->name('boards.stories.send-to-testing');
            Route::post('boards/{board}/testing-cards/{testing_card}/assign', [KanbanTestingCardController::class, 'assign'])
                ->name('boards.testing-cards.assign');
            Route::post('boards/{board}/testing-cards/{testing_card}/move', [KanbanTestingCardController::class, 'move'])
                ->name('boards.testing-cards.move');
            Route::post('boards/{board}/testing-cards/{testing_card}/result', [KanbanTestingCardController::class, 'result'])
                ->name('boards.testing-cards.result');
            Route::post('boards/{board}/bugs', [KanbanBugController::class, 'store'])->name('boards.bugs.store');
            Route::patch('boards/{board}/bugs/{bug}', [KanbanBugController::class, 'update'])->name('boards.bugs.update');
            Route::post('boards/{board}/documents', [KanbanBoardDocumentController::class, 'store'])->name('boards.documents.store');
            Route::get('boards/{board}/documents/{document}', [KanbanBoardDocumentController::class, 'download'])
                ->name('boards.documents.download');
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
