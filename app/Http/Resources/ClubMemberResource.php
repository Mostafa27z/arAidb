<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student' => [
                'id' => $this->student->id,
                'name' => $this->student->name,
            ],
            'status'=> $this->status
            ,
            'club' => [
                'id' => $this->club->id,
                'name' => $this->club->name,
            ],
            'joined_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
