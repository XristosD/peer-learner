<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\BookFactory;
use Database\Factories\NoteFactory;
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
        
        BookFactory::new()->count(25)->for($user)->public()->create()->each(function ($book) {
            NoteFactory::new()->count(30)->for($book)->create();
        });
    }
}
