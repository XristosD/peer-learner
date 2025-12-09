<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NoteTagsCollection extends ResourceCollection
{

    public static $wrap = null;
    
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($tag) {
            return [
                'ulid' => $tag->ulid,
                'title' => $tag->title,
                'slug' => $tag->slug,
                'order' => $tag->pivot->order,
            ];
        })->all();
    }
}
