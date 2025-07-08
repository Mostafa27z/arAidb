<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'exam_question_id',
        'selected_option_id',
        'essay_answer',
        'score',
    ];

    // العلاقات
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // App\Models\StudentExamAnswer.php

public function question()
{
    return $this->belongsTo(ExamQuestion::class, 'exam_question_id');
}



    public function selectedOption()
    {
        return $this->belongsTo(ExamQuestionOption::class, 'selected_option_id');
    }
}
