<?php

use App\Enums\BookVisibility;
use App\Models\Book;
use App\Models\User;

describe('Book Model', function () {

    it('allows a user to have many books', function () {
        $user = User::factory()->create();

        Book::factory()->count(3)->for($user)->create();

        expect($user->books)->toHaveCount(4); // 3 + 1 default book
    });

    it('creates a default book on user registration', function () {
        $user = User::factory()->create();

        expect($user->books)->toHaveCount(1);
        expect($user->books->first()->title)->toBe('Default Book');
    });

    it('associates a book with its user', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        expect($book->user->is($user))->toBeTrue();
    });

    it('generates a slug from the book title', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create([
            'title' => 'My Awesome Book',
        ]);

        expect($book->slug)->toBe('my-awesome-book');
    });

    it('checks slugs can be duplicated', function () {
        $user = User::factory()->create();
        $book1 = Book::factory()->for($user)->create([
            'title' => 'Duplicate Title',
        ]);
        $book2 = Book::factory()->for($user)->create([
            'title' => 'Duplicate Title',
        ]);

        expect($book1->slug)->toBe('duplicate-title');
        expect($book2->slug)->toBe('duplicate-title');
    });

    it('generates an ulid for the book', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        expect($book->ulid)->not->toBeNull();
        expect(strlen($book->ulid))->toBe(26);
    });

    it('uses ulid for route key', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        expect($book->getRouteKey())->toBe($book->ulid);
    });

    it('deletes books when the user is deleted', function () {
        $user = User::factory()->create();
        Book::factory()->count(2)->for($user)->create();

        expect(Book::where('user_id', $user->id)->get())->toHaveCount(3);

        $book_ids = $user->books->pluck('id')->toArray();

        $user->delete();

        expect(Book::whereIn('id', $book_ids)->get())->toHaveCount(0);
    });

    it('checks book visibility enum', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create([
            'visibility' => BookVisibility::Public,
        ]);

        expect($book->visibility)->toBe(BookVisibility::Public);
    });

    it('sets default book visibility to private', function () {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        expect($book->visibility)->toBe(BookVisibility::Private);
    });
});
