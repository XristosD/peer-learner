<?php

use App\Models\Book;
use App\Models\Note;
use App\Models\User;

describe('Note Model', function () {
    it('can be created with valid attributes', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $note = Note::factory()->for($book)->create([
            'body' => 'This is a test note.',
            'details' => 'These are additional details for the test note.',
        ]);

        expect($note)->toBeInstanceOf(Note::class);
        expect($note->body)->toBe('This is a test note.');
        expect($note->details)->toBe('These are additional details for the test note.');
        expect($note->book->id)->toBe($book->id);
    });

    it('belongs to a book', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();
        $note = Note::factory()->for($book)->create();

        expect($note->book)->toBeInstanceOf(Book::class);
        expect($note->book->id)->toBe($book->id);
    });

    it('generates an ulid for the note', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();
        $note = Note::factory()->for($book)->create();

        expect($note->ulid)->not->toBeNull();
        expect(strlen($note->ulid))->toBe(26);
    });

    it('deletes the note on book deletion', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();
        $note = Note::factory()->for($book)->create();

        $bookId = $book->id;
        $noteId = $note->id;

        $book->delete();

        expect(Note::find($noteId))->toBeNull();
    });
});
