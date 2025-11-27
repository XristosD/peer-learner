<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ulid' => $this->ulid,
            'body' => $this->body,
            'details' => $this->details,
            'book_ulid' => $this->book->ulid,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
