<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): \Illuminate\Contracts\Support\Arrayable|\JsonSerializable|array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'creation_date' => $this->created_at->format('Y-m-d H:i:s'),
            'views' => $this->userviews_count,
            'rating' => $this->calculated_rate,
        ];
    }
}
