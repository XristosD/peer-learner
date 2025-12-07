<?php

namespace App\Actions\Book;

use App\Models\Book;
use App\Models\User;

class CreateDefaultBookAction
{
    /**
     * Create a default book for the given user.
     */
    public function execute(User $user): Book
    {
        return Book::create([
            'title' => 'Default Book',
            'user_id' => $user->id,
            'is_default' => true,
        ]);
    }
}
