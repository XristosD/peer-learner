<?php

namespace App\Actions\Book;

use App\Models\Book;
use App\Models\User;

class UnsetDefaultBookAction
{
    public function execute(User $user): void
    {
        // Clear default flag from all other books for this user
        Book::where('user_id', $user->id)
            ->update(['is_default' => false]);
    }
}