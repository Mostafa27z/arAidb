<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseEnrollmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'student' => $this->student,
            'course' => $this->course,
            'status' => $this->status,
            'enrolled_at' => $this->enrolled_at,
            'created_at' => $this->created_at,
        ];
    }

}
