<?php

namespace App\Actions\User;

use App\Models\Book;
use App\Models\User;

class CreateDefaultBook
{
    /**
     * Create a default book for the given user.
     */
    public function execute(User $user): Book
    {
        return Book::create([
            'title' => 'Default Book',
            'user_id' => $user->id,
        ]);
    }
}
