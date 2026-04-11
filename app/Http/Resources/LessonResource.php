<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'duration_minutes' => $this->duration_minutes,
            'order_index' => $this->order_index,
            'content' => $this->when($this->relationLoaded('course'), $this->content),
            'video_url' => $this->when($this->relationLoaded('course'), $this->video_url),
        ];
    }
}
