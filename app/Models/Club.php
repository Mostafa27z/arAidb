<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'teacher_id'
    ];
    public function members()
    {
        return $this->hasMany(ClubMember::class);
    }
    public function messages()
{
    return $this->hasMany(ClubChatMessage::class);
}
public function teacher()
{
    return $this->belongsTo(Teacher::class);
}

}
