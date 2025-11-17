<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BookController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('book')->group(function () {
    Route::get('{book?}/{slug?}', [BookController::class, 'show'])->middleware(['auth', 'verified'])->name('book.show');
    Route::post('', [BookController::class, 'create'])->middleware(['auth', 'verified'])->name('book.create');
    Route::put('{book}', [BookController::class, 'update'])->middleware(['auth', 'verified'])->name('book.update');
    Route::prefix('{book}/note')->group(function () {
        Route::post('', [BookController::class, 'createNote'])->middleware(['auth', 'verified'])->name('book.note.create');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
