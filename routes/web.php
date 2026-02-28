<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\RelationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\EventReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RoleCheckController;
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\ImageController;

Route::get('/clear-caches', function () {
    
    // Clear route cache
    Artisan::call('route:clear');

    // Clear configuration cache
    Artisan::call('config:clear');

    // Clear view cache
    Artisan::call('view:clear');

    // Clear application cache
    Artisan::call('cache:clear');

    // Optimize and cache routes
    Artisan::call('route:cache');

    // Optimize and cache configuration
    Artisan::call('config:cache');
    
    // Optimize clear
    Artisan::call('optimize:clear');

    return 'Caches cleared and optimized!';

});

Route::get('/', function () {
     return redirect()->route('login');
 })->name('home');

 Route::get('/login', function () {
     return view('login');
 });

// Shortcut route: /user should send authenticated users to their dashboard,
// otherwise send them to the login page.
Route::get('/user', function () {
    return redirect()->route('user.dashboard');
})->middleware(['auth', \App\Http\Middleware\RedirectIfAdmin::class]);

Route::get('/dashboard', function () {
    $totalMembers = \App\Models\User::count();
    return view('admin.dashboard', compact('totalMembers'));
})->middleware(['auth', 'verified'])->name('dashboard');

// User-facing dashboard (for regular users after login)
Route::get('/user/dashboard', function () {
    $totalMembers = \App\Models\User::count();
    $events = \App\Models\Event::orderBy('id', 'desc')->get();
    return view('admin.userdashboard', compact('totalMembers', 'events'));
})->middleware(['auth', 'verified'])->name('user.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Foods resource (index, create, store, etc.)
    Route::get('foods/list', [FoodController::class, 'list'])->name('foods.list');
    Route::resource('foods', FoodController::class);
    // Chapters resource (index, list)
    Route::get('chapter/list', [ChapterController::class, 'list'])->name('chapter.list');
    Route::resource('chapter', ChapterController::class);
    // Relations resource (index, list)
    Route::get('relations/list', [RelationController::class, 'list'])->name('relations.list');
    Route::resource('relations', RelationController::class);
    // Events resource (index, list)
    Route::get('events/list', [EventController::class, 'list'])->name('events.list');
    Route::resource('events', EventController::class);
    // Users resource (index, list)
    Route::get('users/list', [UserController::class, 'list'])->name('users.list');
    Route::resource('users', UserController::class);
    // Activity logs
    Route::get('activity', [ActivityController::class, 'index'])->name('activity.logs');
    Route::get('activity/list', [ActivityController::class, 'list'])->name('activity.list');
    Route::get('activity/{id}', [ActivityController::class, 'show'])->name('activity.show');
    Route::get('/reports/event',[EventReportController::class, 'index'])->name('reports.event');
    Route::get('/reports/event/export', [EventReportController::class, 'export'])->name('reports.event.export');
    Route::get('/reports/event/export-excel', [EventReportController::class, 'exportExcel'])
        ->name('reports.event.exportExcel');
        Route::get('password/change', [\App\Http\Controllers\Auth\PasswordController::class, 'edit'])->name('password.change');
});

Route::get('registrations/create', [RegistrationController::class, 'create'])
    ->middleware('auth')
    ->name('registrations.create');

// Store registration submissions
Route::post('registrations', [RegistrationController::class, 'store'])
    ->middleware('auth')
    ->name('registrations.store');
    
Route::get('registrations/debug', function () {
    return view('admin.eventsForm.create');
});

require __DIR__.'/auth.php';

// AJAX: check role by email (used by two-step login form)
Route::post('/check-role', [RoleCheckController::class, 'checkRole']);

// User login via email + mobile (regular users)
Route::post('/user-login', [UserLoginController::class, 'loginWithMobile']);

// Serve storage images when public/storage symlink is missing. Path is URL-safe base64.
Route::get('/storage-image/{encoded}', [ImageController::class, 'storageImage'])->where('encoded', '.*')->name('storage.image');
