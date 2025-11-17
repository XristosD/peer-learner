<?php

namespace App\Http\Controllers;

use App\Actions\Book\UpdateBookAction;
use App\Actions\Book\CreateBookAction;
use App\Actions\BookNote\CreateNoteAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\CreateNoteRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Requests\UpdateBookRequest;

class BookController extends Controller
{
    public function show(Request $request, ?Book $book = null)
    {
        if (is_null($book)) {
            $book = Auth::user()->books()->latest()->first();
            return redirect()->route('book.show', [ 'book' => $book->ulid, 'slug' => $book->slug ]);
        }
        return Inertia::render('books/Book', [
            'notes' => fn() => $book->notes()->latest()->get(),
            'book' => fn() =>$book,
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book, UpdateBookAction $updateBookAction)
    {
        $updateBookAction->execute($book, $request->validated());

        return redirect()->route('book.show', [ 'book' => $book->ulid, 'slug' => $book->slug ]);
    }

        public function create(CreateBookRequest $request, CreateBookAction $createBookAction)
    {
        $book = $createBookAction->execute($request->validated());

        return redirect()->route('book.show', [ 'book' => $book->ulid, 'slug' => $book->slug ]);
    }

    public function createNote(CreateNoteRequest $request, Book $book, CreateNoteAction $createNoteAction)
    {
        $createNoteAction->execute($book, $request->validated());

        return redirect()->route('book.show', [ 'book' => $book->ulid, 'slug' => $book->slug ]);
    }
}
