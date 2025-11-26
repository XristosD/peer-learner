<?php

namespace App\Http\Controllers;

use App\Actions\Book\CreateBookAction;
use App\Actions\Book\UpdateBookAction;
use App\Actions\Note\CreateNoteAction as NoteCreateNoteAction;
use App\Actions\Note\UpdateNoteAction;
use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\CreateNoteRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Book;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookController extends Controller
{
    public function show(Request $request, ?Book $book = null)
    {
        if (is_null($book)) {
            $book = Auth::user()->books()->latest()->first();

            return redirect()->route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]);
        }

        return Inertia::render('books/Book', [
            'notes' => fn() => $book->notes()->latest()->get(),
            'book' => fn() => $book,
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book, UpdateBookAction $updateBookAction)
    {
        $updateBookAction->execute($book, $request->validated());

        return redirect()->route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]);
    }

    public function create(CreateBookRequest $request, CreateBookAction $createBookAction)
    {
        $book = $createBookAction->execute($request->validated());

        return redirect()->route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]);
    }

    public function createNote(CreateNoteRequest $request, Book $book, NoteCreateNoteAction $createNoteAction)
    {
        $createNoteAction->execute($book, $request->validated());

        return redirect()->route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]);
    }

    public function updateNote(UpdateNoteRequest $request, Book $book, Note $note, UpdateNoteAction $updateNoteAction)
    {
        $updateNoteAction->execute($note, $request->validated());

        return redirect()->route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]);
    }
}
