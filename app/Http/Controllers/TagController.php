<?php

namespace App\Http\Controllers;

use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Search for tags by query string.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->string('q')->trim();
        $excludeIds = $request->array('exclude', []);

        if (blank($query)) {
            return response()->json([]);
        }

        $tags = Tag::where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('slug', 'like', "%{$query}%");
            })
            ->whereNotIn('ulid', $excludeIds)
            ->orderBy('title')
            ->limit(10)
            ->get();

        return response()->json(TagResource::collection($tags));
    }
}
