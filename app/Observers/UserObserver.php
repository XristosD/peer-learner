<?php

namespace App\Observers;

use App\Actions\Book\CreateDefaultBookAction;
use App\Models\User;

class UserObserver
{
    public function __construct(
        private readonly CreateDefaultBookAction $createDefaultBookAction,
    ) {}

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->createDefaultBookAction->execute($user);
    }
}
