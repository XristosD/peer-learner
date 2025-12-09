<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\BookFactory;
use Database\Factories\NoteFactory;
use Database\Factories\TagFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@email.com',
            'password' => 'password',
        ]);

        $tags = TagFactory::new()->count(50)->create();

        BookFactory::new()->count(25)->for($user)->public()->create()->each(function ($book) use ($tags) {
            NoteFactory::new()->count(30)->for($book)->create()->each(function ($note) use ($tags) {
                $randomTags = $tags->random(random_int(0, 5));

                $note->tags()->attach(
                    $randomTags->mapWithKeys(function ($tag, $index) use ($note) {
                        return [
                            $tag->id => [
                                'book_id' => $note->book_id,
                                'order' => $index,
                            ],
                        ];
                    })->all()
                );
            });
        });
    }
}
