<?php

namespace App\Actions\Note;

use App\Models\Note;

class UpdateNoteAction
{
    public function execute(Note $note, array $data): Note
    {
        $note->update($data);

        return $note;
    }
}
