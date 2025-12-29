<?php

namespace App\Actions\Note;

use App\DTOs\NoteDataDTO;
use App\Models\Book;
use App\Models\Note;

class CreateNoteAction
{
    public function execute(Book $book, NoteDataDTO $data): Note
    {
        return $book->notes()->create($data->toArray());
    }
}
