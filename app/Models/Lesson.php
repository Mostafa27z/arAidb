<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Lesson extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'content',
        'attachment'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // نضيف علاقة الواجبات:
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'lesson_id');
    }
}
