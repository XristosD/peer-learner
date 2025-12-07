<?php

namespace App\Actions\Book;

use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class CreateBookAction
{

    public function __construct(
        protected UnsetDefaultBookAction $unsetDefaultBookAction
    ) {}
    
    public function execute(array $data): Book
    {
        if (isset($data['is_default']) && $data['is_default']) {
            // Clear default flag from all other books for this user
            $this->unsetDefaultBookAction->execute(Auth::user());
        }
        $book = Auth::user()->books()->create($data);

        return $book;
    }
}
