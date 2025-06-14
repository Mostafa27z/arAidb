<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseEnrollmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'student'     => [
                'id'         => $this->student->id,
                'user_id'    => $this->student->student_id,  // ده هو user_id
                'name'       => optional($this->student->user)->name,
                'email'      => optional($this->student->user)->email,
                'grade'      => $this->student->grade_level,
            ],
            'course'      => [
                'id'          => $this->course->id,
                'title'       => $this->course->title,
                'description' => $this->course->description,
            ],
            'status'      => $this->status,
            'enrolled_at' => $this->enrolled_at,
            'created_at'  => $this->created_at,
        ];
    }
}
