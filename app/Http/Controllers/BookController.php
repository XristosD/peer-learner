<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class BookController extends Controller
{
    public function show(Request $request, ?Book $book = null)
    {
        if (is_null($book)) {
            $book = Auth::user()->books()->latest()->first();
            return redirect()->route('book.show', [ 'book' => $book->ulid, 'slug' => $book->slug ]);
        }
        return Inertia::render('books/Book', [
            'notes' => $book->notes()->latest()->get(),
            'book' => $book,
        ]);
    }
}
