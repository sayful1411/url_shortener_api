<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UrlResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'url_visit' => $this->when(
                $this->whenNotNull($this->url_visit),
                new UrlVisitResource($this->whenLoaded('url_visit'))
            ),
            'short_code' => $this->short_code,
            'original_url' => $this->original_url
        ];
    }
}
