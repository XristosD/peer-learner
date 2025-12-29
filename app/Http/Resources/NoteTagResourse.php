<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteTagResourse extends JsonResource
{
    
    /**
     * Transform the resource into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ulid' => $this->ulid,
            'title' => $this->title,
            'slug' => $this->slug,
            'order' => $this->pivot->order,
        ];
    }
}
