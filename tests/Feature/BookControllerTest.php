<?php

use App\Models\Book;
use App\Models\Note;
use App\Models\User;
use function Pest\Laravel\{actingAs, get, post, put, assertDatabaseMissing, assertDatabaseHas};

describe('BookController', function () {

    // ===== BookController::show() =====
    // Tests for displaying a book and its notes

    it('redirects to the latest book when no book parameter is provided', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $latestBook = $user->books()->latest()->first();

        $response = actingAs($user)->get(route('book.show'));

        $response->assertRedirect(route('book.show', [
            'book' => $latestBook->ulid,
            'slug' => $latestBook->slug,
        ]));
    });

    it('displays a specific book with its notes', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create(['title' => 'Test Book']);
        
        $notes = Note::factory()->count(3)->for($book)->create();

        $response = actingAs($user)
            ->get(route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('books/Book')
            ->has('book')
            ->where('book.ulid', $book->ulid)
            ->where('book.title', 'Test Book')
            ->has('notes', 3)
        );
    });

    it('loads notes in the correct order (latest first)', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();
        
        $oldNote = Note::factory()->for($book)->create(['created_at' => now()->subDays(2)]);
        $newNote = Note::factory()->for($book)->create(['created_at' => now()]);

        $response = actingAs($user)
            ->get(route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('books/Book')
            ->has('notes', 2)
            ->where('notes.0.ulid', $newNote->ulid)
            ->where('notes.1.ulid', $oldNote->ulid)
        );
    });

    it('displays a book with no notes', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $response = actingAs($user)
            ->get(route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('books/Book')
            ->has('book')
            ->where('book.ulid', $book->ulid)
            ->has('notes', 0)
        );
    });

    it('requires authentication to view a book', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $response = get(route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]));

        $response->assertRedirect(route('login'));
    });

    it('requires authentication to redirect to latest book', function () {
        $response = get(route('book.show'));

        $response->assertRedirect(route('login'));
    });

    it('handles multiple books and redirects to the most recent', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        
        $book1 = Book::factory()->for($user)->create(['created_at' => now()->subDays(5)]);
        $book2 = Book::factory()->for($user)->create(['created_at' => now()->subDays(2)]);
        $newestBook = $user->books()->latest()->first();

        $response = actingAs($user)->get(route('book.show'));

        $response->assertRedirect(route('book.show', [
            'book' => $newestBook->ulid,
            'slug' => $newestBook->slug,
        ]));
    });

    it('uses the book ulid as the route key', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $response = actingAs($user)
            ->get(route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]));

        $response->assertOk();
    });

    it('passes the correct book instance to the view', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create(['title' => 'Unique Book Title']);

        $response = actingAs($user)
            ->get(route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]));

        $response->assertInertia(fn ($page) => $page
            ->component('books/Book')
            ->where('book.title', 'Unique Book Title')
            ->where('book.slug', $book->slug)
        );
    });

    it('loads only notes belonging to the specified book', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book1 = Book::factory()->for($user)->create();
        $book2 = Book::factory()->for($user)->create();

        $book1Notes = Note::factory()->count(2)->for($book1)->create();
        $book2Notes = Note::factory()->count(3)->for($book2)->create();

        $response = actingAs($user)
            ->get(route('book.show', ['book' => $book1->ulid, 'slug' => $book1->slug]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('notes', 2)
        );
    });

    // ===== BookController::update() =====
    // Tests for updating a book
    // (No tests implemented yet)

    // ===== BookController::create() =====
    // Tests for creating a book
    // (No tests implemented yet)

    // ===== BookController::createNote() =====
    // Tests for creating a note within a book

    it('creates a note for a book with valid data', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $noteData = [
            'body' => 'This is a test note body',
            'details' => 'These are additional details',
        ];

        $response = actingAs($user)
            ->post(route('book.note.create', ['book' => $book->ulid, 'slug' => $book->slug]), $noteData);

        $response->assertRedirect(route('book.show', [
            'book' => $book->ulid,
            'slug' => $book->slug,
        ]));

        assertDatabaseHas('notes', [
            'book_id' => $book->id,
            'body' => 'This is a test note body',
            'details' => 'These are additional details',
        ]);
    });

    it('creates a note without optional details', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $noteData = [
            'body' => 'Note without details',
        ];

        $response = actingAs($user)
            ->post(route('book.note.create', ['book' => $book->ulid, 'slug' => $book->slug]), $noteData);

        $response->assertRedirect(route('book.show', [
            'book' => $book->ulid,
            'slug' => $book->slug,
        ]));

        assertDatabaseHas('notes', [
            'book_id' => $book->id,
            'body' => 'Note without details',
            'details' => null,
        ]);
    });

    it('requires authentication to create a note', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $noteData = [
            'body' => 'This note should not be created',
        ];

        $response = post(route('book.note.create', ['book' => $book->ulid, 'slug' => $book->slug]), $noteData);

        $response->assertRedirect(route('login'));
        assertDatabaseMissing('notes', [
            'body' => 'This note should not be created',
        ]);
    });

    it('validates that body is required when creating a note', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $noteData = [
            'details' => 'Details without body',
        ];

        $response = actingAs($user)
            ->post(route('book.note.create', ['book' => $book->ulid, 'slug' => $book->slug]), $noteData);

        $response->assertSessionHasErrors('body');
        assertDatabaseMissing('notes', [
            'details' => 'Details without body',
        ]);
    });

    it('validates that body must be a string when creating a note', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $noteData = [
            'body' => ['not', 'a', 'string'],
        ];

        $response = actingAs($user)
            ->post(route('book.note.create', ['book' => $book->ulid, 'slug' => $book->slug]), $noteData);

        $response->assertSessionHasErrors('body');
    });

    it('validates that details must be a string when creating a note', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $noteData = [
            'body' => 'Valid body',
            'details' => 12345,
        ];

        $response = actingAs($user)
            ->post(route('book.note.create', ['book' => $book->ulid, 'slug' => $book->slug]), $noteData);

        $response->assertSessionHasErrors('details');
    });

    it('creates a note using the correct book instance', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book1 = Book::factory()->for($user)->create();
        $book2 = Book::factory()->for($user)->create();

        $noteData = [
            'body' => 'Note for book 1',
        ];

        $response = actingAs($user)
            ->post(route('book.note.create', ['book' => $book1->ulid, 'slug' => $book1->slug]), $noteData);

        $response->assertRedirect(route('book.show', [
            'book' => $book1->ulid,
            'slug' => $book1->slug,
        ]));

        assertDatabaseHas('notes', [
            'book_id' => $book1->id,
            'body' => 'Note for book 1',
        ]);

        assertDatabaseMissing('notes', [
            'book_id' => $book2->id,
            'body' => 'Note for book 1',
        ]);
    });

    it('increments note count for the book after creating a note', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $initialCount = $book->notes()->count();

        $noteData = [
            'body' => 'New note',
        ];

        actingAs($user)
            ->post(route('book.note.create', ['book' => $book->ulid, 'slug' => $book->slug]), $noteData);

        expect($book->fresh()->notes()->count())->toBe($initialCount + 1);
    });

    // ===== BookController::updateNote() =====
    // Tests for updating a note within a book

    it('updates a note with valid data', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();
        $note = Note::factory()->for($book)->create(['body' => 'Original body', 'details' => 'Original details']);

        $updatedData = [
            'body' => 'Updated note body',
            'details' => 'Updated details',
        ];

        $response = actingAs($user)
            ->put(route('book.note.update', ['book' => $book->ulid, 'slug' => $book->slug, 'note' => $note->ulid]), $updatedData);

        $response->assertRedirect(route('book.show', [
            'book' => $book->ulid,
            'slug' => $book->slug,
        ]));

        assertDatabaseHas('notes', [
            'ulid' => $note->ulid,
            'body' => 'Updated note body',
            'details' => 'Updated details',
        ]);
    });

    it('updates a note and clears optional details', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();
        $note = Note::factory()->for($book)->create(['body' => 'Original body', 'details' => 'Original details']);

        $updatedData = [
            'body' => 'Updated body without details',
            'details' => null,
        ];

        $response = actingAs($user)
            ->put(route('book.note.update', ['book' => $book->ulid, 'slug' => $book->slug, 'note' => $note->ulid]), $updatedData);

        $response->assertRedirect();

        assertDatabaseHas('notes', [
            'ulid' => $note->ulid,
            'body' => 'Updated body without details',
            'details' => null,
        ]);
    });

    it('requires authentication to update a note', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();
        $note = Note::factory()->for($book)->create();

        $updatedData = [
            'body' => 'Updated body',
        ];

        $response = put(route('book.note.update', ['book' => $book->ulid, 'slug' => $book->slug, 'note' => $note->ulid]), $updatedData);

        $response->assertRedirect(route('login'));
    });

    it('validates that body is required when updating a note', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();
        $note = Note::factory()->for($book)->create(['body' => 'Original body']);

        $updatedData = [
            'details' => 'Details without body',
        ];

        $response = actingAs($user)
            ->put(route('book.note.update', ['book' => $book->ulid, 'slug' => $book->slug, 'note' => $note->ulid]), $updatedData);

        $response->assertSessionHasErrors('body');
    });

    it('validates that body must be a string when updating a note', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();
        $note = Note::factory()->for($book)->create();

        $updatedData = [
            'body' => ['not', 'a', 'string'],
        ];

        $response = actingAs($user)
            ->put(route('book.note.update', ['book' => $book->ulid, 'slug' => $book->slug, 'note' => $note->ulid]), $updatedData);

        $response->assertSessionHasErrors('body');
    });

    it('validates that details must be a string when updating a note', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();
        $note = Note::factory()->for($book)->create();

        $updatedData = [
            'body' => 'Valid body',
            'details' => 12345,
        ];

        $response = actingAs($user)
            ->put(route('book.note.update', ['book' => $book->ulid, 'slug' => $book->slug, 'note' => $note->ulid]), $updatedData);

        $response->assertSessionHasErrors('details');
    });

    it('updates only the specified note', function () {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();
        $note1 = Note::factory()->for($book)->create(['body' => 'Note 1']);
        $note2 = Note::factory()->for($book)->create(['body' => 'Note 2']);

        $updatedData = [
            'body' => 'Updated Note 1',
        ];

        actingAs($user)
            ->put(route('book.note.update', ['book' => $book->ulid, 'slug' => $book->slug, 'note' => $note1->ulid]), $updatedData);

        assertDatabaseHas('notes', [
            'ulid' => $note1->ulid,
            'body' => 'Updated Note 1',
        ]);

        assertDatabaseHas('notes', [
            'ulid' => $note2->ulid,
            'body' => 'Note 2',
        ]);
    });

});
