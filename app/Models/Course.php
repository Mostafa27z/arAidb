<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'teacher_id',
        'thumbnail'
    ];


    // public function teacher()
    // {
    //     return $this->belongsTo(Teacher::class);
    // }

    // public function lessons()
    // {
    //     return $this->hasMany(Lesson::class);
    // }

    // public function enrollments()
    // {
    //     return $this->hasMany(CourseEnrollment::class);
    // }
    public function teacher()
{
    return $this->belongsTo(Teacher::class, 'teacher_id');
}

public function lessons()
{
    return $this->hasMany(Lesson::class, 'course_id');
}

public function enrollments()
{
    return $this->hasMany(CourseEnrollment::class, 'course_id');
}
public function approvedEnrollments()
{
    return $this->hasMany(CourseEnrollment::class, 'course_id')
                ->where('status', 'approved');
}
public function assignments() { return $this->hasMany(Assignment::class); }
}
