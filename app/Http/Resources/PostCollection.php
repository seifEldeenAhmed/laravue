<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'links' => [
                'self' => url('/api/posts'),
                'next' => $this->nextPageUrl(),
                'prev' => $this->previousPageUrl(),
            ],
            'meta' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'count' => $this->collection->count(),
                'drafted' => $this->collection->where('status', 'draft')->count(),
                'published' => $this->collection->where('status', 'published')->count()
            ],
        ];
    }
}
