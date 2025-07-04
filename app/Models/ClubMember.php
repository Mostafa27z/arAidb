<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'club_id',
        'status'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
    public function chatMessages()
{
    return $this->hasMany(ClubChatMessage::class, 'club_id');
}

    public function members()
    {
        return $this->hasMany(ClubMember::class, 'club_id');
    }

}
