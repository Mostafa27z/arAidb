<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    public function toArray($request)
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'description' => $this->description,
        'due_date' => $this->due_date,
        'attachment' => $this->attachment,
        'lesson_id' => $this->lesson_id,
        'lesson_title' => optional($this->lesson)->title,
        'course_title' => optional($this->lesson->course)->title ?? null,
    ];
}

}
