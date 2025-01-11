<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tasks\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks');
    Route::post('/create-task', [TaskController::class, 'create'])->name('create-task');
    Route::delete('/tasks/{task}', [TaskController::class, 'delete']);
    Route::post('/tasks/{task}/assign', [TaskController::class, 'assignToMe'])->name('tasks.assign');
    Route::post('/tasks/{task}/unassign', [TaskController::class, 'unassignFromMe'])->name('tasks.unassign');
    Route::post('/update-task-status/{task}', [TaskController::class, 'updateStatus'])->name('update-task-status');
    Route::get('/tasks/show/{id}', [TaskController::class, 'show'])->name('tasks.show');
});

require __DIR__.'/auth.php';
