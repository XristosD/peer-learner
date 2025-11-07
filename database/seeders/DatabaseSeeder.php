<?php

namespace Database\Seeders;

use App\Models\User;
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

        NoteFactory::new()->count(15)->for($user->books->first())->create();
    }
}
