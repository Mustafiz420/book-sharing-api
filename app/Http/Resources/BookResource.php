<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'description' => $this->description,
            'user_id' => $this->when(!isset($this->distance_km) && isset($this->user_id), (int) $this->user_id),
            'user' => $this->when(
                $this->relationLoaded('user') || isset($this->user_name),
                function () {
                    return [
                        'id' => isset($this->user_id) ? (int) $this->user_id : null,
                        'name' => $this->user_name ?? null,
                    ];
                }
            ),
            'distance_km' => $this->when(isset($this->distance_km), fn () => round((float) $this->distance_km, 2)),
        ];
    }
}
