<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'track' => $this->track,
            'description' => $this->description,
            'duration_hours' => $this->duration_hours,
            'lessons_count' => $this->lessons_count,
            'is_active' => $this->is_active,
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
        ];
    }
}
