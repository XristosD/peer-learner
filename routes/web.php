<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\NoteController;
use App\Models\Note;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::prefix('book')->group(function () {
    Route::get('{book?}/{slug?}', [BookController::class, 'show'])->middleware(['auth', 'verified'])->name('book.show');
    Route::post('', [BookController::class, 'create'])->middleware(['auth', 'verified'])->name('book.create');
    Route::put('{book}', [BookController::class, 'update'])->middleware(['auth', 'verified'])->name('book.update');
    Route::prefix('{book}/note')->group(function () {
        Route::get('{note}', [NoteController::class, 'show'])->middleware(['auth', 'verified'])->name('book.note.show');
        Route::get('{note}/edit', [NoteController::class, 'edit'])->middleware(['auth', 'verified'])->name('book.note.edit');
        Route::post('{note}/edit', [NoteController::class, 'update'])->middleware(['auth', 'verified'])->name('book.note.update');
        Route::post('', [NoteController::class, 'create'])->middleware(['auth', 'verified'])->name('book.note.create');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
