<?php

namespace App\Actions\Book;

use App\Models\Book;

class UpdateBookAction
{

    public function __construct(
        protected UnsetDefaultBookAction $unsetDefaultBookAction
    ) {}

    public function execute(Book $book, array $data): Book
    {
        if (isset($data['is_default']) && $data['is_default']) {
            // Clear default flag from all other books for this user
            $this->unsetDefaultBookAction->execute($book->user);
        }
        
        $book->update($data);

        return $book;
    }
}
