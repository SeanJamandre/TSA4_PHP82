<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
Route::view('/', 'welcome')->name('welcome');
Route::view('/about', 'about')->name('about');
Route::view('/profile', 'profile-public')->name('profile.public');
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('tasks.index'))->name('dashboard');
    Route::resource('tasks', TaskController::class)->except(['index','show']);
    Route::get('/account', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/account', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/account', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
require __DIR__.'/auth.php';
