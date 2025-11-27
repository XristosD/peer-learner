<?php

namespace App\Http\Controllers;

use App\Actions\Note\CreateNoteAction;
use App\Actions\Note\UpdateNoteAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\NoteResource;
use App\Models\Book;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function show(Request $request, Book $book, Note $note)
    {
        return inertia('notes/Show', [
            'book' => fn() => new BookResource($book)->toArray($request),
            'note' => fn() => new NoteResource($note)->toArray($request),
        ]);
    }

    public function edit(Request $request, Book $book, Note $note)
    {
        return inertia('notes/Edit', [
            'book' => fn() => new BookResource($book)->toArray($request),
            'note' => fn() => new NoteResource($note)->toArray($request),
        ]);
    }

    public function update(UpdateNoteRequest $request, Book $book, Note $note, UpdateNoteAction $updateNoteAction)
    {
        $updateNoteAction->execute($note, $request->validated());

        return redirect()->route('book.note.show', ['book' => $book->ulid, 'note' => $note->ulid]);
    }

    public function create(CreateNoteRequest $request, Book $book, CreateNoteAction $createNoteAction)
    {
        $createNoteAction->execute($book, $request->validated());

        return redirect()->route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]);
    }
}
