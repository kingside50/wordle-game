<?php
use App\Http\Controllers\WordleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FriendController;
use Illuminate\Support\Facades\Route;





Route::middleware('auth')->group(function () {
    Route::get('/', [WordleController::class, 'index'])->name('wordle.index');
    Route::post('/guess', [WordleController::class, 'guess'])->name('guess');
    Route::post('/friend/add', [FriendController::class, 'add'])->name('friend.add');
    Route::get('/dashboard', [WordleController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
