<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->resource->map(fn ($row) => [
            'id' => $row->id,
            'title' => $row->title,
            'edited_at' => $row->edited_at,
            'is_modified' => $row->is_modified,
            'body' => $row->body,
        ])->toArray();
    }
}
