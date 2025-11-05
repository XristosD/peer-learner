<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create books for existing users
        User::all()->each(function (User $user) {
            Book::factory()
                ->count(3)
                ->for($user)
                ->create();
        });
    }
}
