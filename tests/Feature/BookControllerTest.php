<?php

use App\Models\Book;
use App\Models\Note;
use App\Models\User;

describe('BookController', function () {

    it('redirects to the latest book when no book parameter is provided', function () {
        $user = User::factory()->create();
        $latestBook = $user->books()->latest()->first();

        $response = $this->actingAs($user)->get(route('book.show'));

        $response->assertRedirect(route('book.show', [
            'book' => $latestBook->ulid,
            'slug' => $latestBook->slug,
        ]));
    });

    it('displays a specific book with its notes', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create(['title' => 'Test Book']);
        
        $notes = Note::factory()->count(3)->for($book)->create();

        $response = $this->actingAs($user)
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
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();
        
        $oldNote = Note::factory()->for($book)->create(['created_at' => now()->subDays(2)]);
        $newNote = Note::factory()->for($book)->create(['created_at' => now()]);

        $response = $this->actingAs($user)
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
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $response = $this->actingAs($user)
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

        $response = $this->get(route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]));

        $response->assertRedirect(route('login'));
    });

    it('requires authentication to redirect to latest book', function () {
        $response = $this->get(route('book.show'));

        $response->assertRedirect(route('login'));
    });

    it('handles multiple books and redirects to the most recent', function () {
        $user = User::factory()->create();
        
        $book1 = Book::factory()->for($user)->create(['created_at' => now()->subDays(5)]);
        $book2 = Book::factory()->for($user)->create(['created_at' => now()->subDays(2)]);
        $newestBook = $user->books()->latest()->first();

        $response = $this->actingAs($user)->get(route('book.show'));

        $response->assertRedirect(route('book.show', [
            'book' => $newestBook->ulid,
            'slug' => $newestBook->slug,
        ]));
    });

    it('uses the book ulid as the route key', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $response = $this->actingAs($user)
            ->get(route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]));

        $response->assertOk();
    });

    it('passes the correct book instance to the view', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create(['title' => 'Unique Book Title']);

        $response = $this->actingAs($user)
            ->get(route('book.show', ['book' => $book->ulid, 'slug' => $book->slug]));

        $response->assertInertia(fn ($page) => $page
            ->component('books/Book')
            ->where('book.title', 'Unique Book Title')
            ->where('book.slug', $book->slug)
        );
    });

    it('loads only notes belonging to the specified book', function () {
        $user = User::factory()->create();
        $book1 = Book::factory()->for($user)->create();
        $book2 = Book::factory()->for($user)->create();

        $book1Notes = Note::factory()->count(2)->for($book1)->create();
        $book2Notes = Note::factory()->count(3)->for($book2)->create();

        $response = $this->actingAs($user)
            ->get(route('book.show', ['book' => $book1->ulid, 'slug' => $book1->slug]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->has('notes', 2)
        );
    });

});
