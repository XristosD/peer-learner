<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Note;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create notes for existing books
        Book::all()->each(function (Book $book) {
            Note::factory()
                ->count(5)
                ->for($book)
                ->create();
        });
    }
}
