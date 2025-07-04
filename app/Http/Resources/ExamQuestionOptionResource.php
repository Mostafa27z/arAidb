<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExamQuestionOptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'question_id' => $this->question_id,
            'option_text' => $this->option_text,
            'is_correct'  => $this->is_correct,
        ];
    }
}
