<?php

namespace App\Actions\NoteTag;

use App\DTOs\UpdatedTagDTO;
use App\Models\Note;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class UpdateNoteTagsAction
{
    /**
     * Update tags of a note.
     *
     * @param Note $note
     * @param array<UpdatedTagDTO> $tags
     */
    public function execute(Note $note, array $updatedTags): void
    {
        DB::transaction(function () use ($note, $updatedTags) {
            $attachData = [];
            foreach ($updatedTags as $index => $tagDTO) {
                if ($tagDTO->ulid) {
                    $tag = Tag::where('ulid', $tagDTO->ulid)->firstOrFail();
                } else {
                    $tag = Tag::create(['title' => $tagDTO->title]);
                }
                $attachData[$tag->id] = [
                    'book_id' => $note->book_id,
                    'order' => $tagDTO->order,
                ];
            }
            $note->tags()->sync($attachData);
        });
    }
}