<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LogCollection extends ResourceCollection
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
            'level' => $row->level,
            'method' => $row->method,
            'url' => $row->url,
            'key' => $row->key,
            'response_code' => $row->response_code,
            'message' => $row->message,
            'created_at' => $row->created_at,
        ])->toArray();
    }
}
