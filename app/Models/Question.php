<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\QuestionOption;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'question_text',
        'question_type',
    ];

    // Relationship to the Lesson model
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

}
