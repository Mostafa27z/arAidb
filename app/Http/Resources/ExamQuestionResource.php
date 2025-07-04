<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamQuestionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'exam_id'       => $this->exam_id,
            'question_text' => $this->question_text,
            'type'          => $this->type,
            'options'       => ExamQuestionOptionResource::collection($this->whenLoaded('options')),
        ];
    }
}
