<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'exam_date'  => $this->exam_date,
            'start_time' => $this->start_time,
            'end_time'   => $this->end_time,
            'course'     => new CourseResource($this->whenLoaded('course')),
            'questions'  => ExamQuestionResource::collection($this->whenLoaded('questions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
