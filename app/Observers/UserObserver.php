<?php

namespace App\Observers;

use App\Actions\User\CreateDefaultBook;
use App\Models\User;

class UserObserver
{
    public function __construct(
        private readonly CreateDefaultBook $createDefaultBook,
    ) {}

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->createDefaultBook->execute($user);
    }
}
