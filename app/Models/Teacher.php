<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'qualification',
        'bio'
    ];

    // العلاقة السليمة مع الـ User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // دي الغلطة اللي محتاجة نشيلها 👇 (دي علاقة مش موجودة أصلاً)
    // public function course() 
    // { 
    //     return $this->belongsTo(User::class); 
    // }

    // العلاقة الصح مع الـ Courses:
    public function courses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }
}
