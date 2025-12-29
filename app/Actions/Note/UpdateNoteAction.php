<?php

namespace App\Actions\Note;

use App\DTOs\NoteDataDTO;
use App\Models\Note;

class UpdateNoteAction
{
    public function execute(Note $note, NoteDataDTO $data): Note
    {
        $note->update($data->toArray());

        return $note;
    }
}
