<?php

namespace App\Actions\Note;

use App\Models\Book;
use App\Models\Note;

class CreateNoteAction
{
    public function execute(Book $book, array $data): Note
    {
        return $book->notes()->create($data);
    }
}
