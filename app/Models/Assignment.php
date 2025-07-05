<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Assignment extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'due_date',
		'attachment'
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function submissions()
    {
        return $this->hasMany(StudentSubmission::class, 'assignment_id');
    }
}
