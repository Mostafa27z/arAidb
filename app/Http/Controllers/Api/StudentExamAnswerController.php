<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentExamAnswer;
use Illuminate\Http\Request;
use App\Models\ExamResult;

class StudentExamAnswerController  extends Controller
{
    // Get all answers (admin/teacher)
    public function index()
    {
        return response()->json([
            'status' => 200,
            'data' => StudentExamAnswer::with(['student', 'exam', 'question', 'selectedOption'])->get()
        ]);
    }

    // Store multiple answers (e.g., after student submits exam)
 public function store(Request $request)
{
    $examId = $request->input('exam_id'); // احصل على exam_id مرة واحدة
    $answers = $request->input('answers');

    foreach ($answers as $answer) {
        StudentExamAnswer::updateOrCreate(
            [
                'student_id' => $answer['student_id'],
                'exam_id' => $examId, // خذه من الطلب الرئيسي
                'exam_question_id' => $answer['exam_question_id'],
            ],
            [
                'selected_option_id' => $answer['selected_option_id'] ?? null,
                'essay_answer' => $answer['essay_answer'] ?? null,
                'score' => $answer['score'] ?? null,
            ]
        );
    }

    return response()->json([
        'status' => 201,
        'message' => 'تم إرسال الإجابات بنجاح',
    ]);
}



    // Get answers for a specific exam & student (for review)
    // Get answers of a student for a specific exam
public function getAnswersForExam($examId, $studentId)
{
    $answers = StudentExamAnswer::with(['question', 'question.options'])
        ->where('exam_id', $examId)
        ->where('student_id', $studentId)
        ->get();

    return response()->json(['status' => 200, 'data' => $answers]);
}

public function getStudentsWhoAnswered($examId)
{
    $students = StudentExamAnswer::where('exam_id', $examId)
        ->with('student.user')
        ->select('student_id', \DB::raw('MIN(created_at) as created_at')) // أول مرة جاوب فيها
        ->groupBy('student_id')
        ->get();

    return response()->json([
        'status' => 200,
        'data' => $students
    ]);
}

    // Update score for a specific essay answer
    

public function updateScore(Request $request, $id)
{
    $request->validate([
        'score' => 'required|numeric|min:0'
    ]);

    $answer = StudentExamAnswer::with('question.options')->findOrFail($id);
    $answer->update(['score' => $request->score]);

    // ✅ حساب النتيجة الإجمالية بعد التعديل
    $studentId = $answer->student_id;
    $examId = $answer->exam_id;

    $allAnswers = StudentExamAnswer::with('question.options')
        ->where('student_id', $studentId)
        ->where('exam_id', $examId)
        ->get();

    $mcqScore = 0;
    $essayScore = 0;
    $totalMarks = 0;

    foreach ($allAnswers as $ans) {
        if ($ans->question->type === 'mcq') {
            $correctOption = $ans->question->options->firstWhere('is_correct', true);
            if ($correctOption && $ans->selected_option_id == $correctOption->id) {
                $mcqScore += 1;
            }
            $totalMarks += 1;
        } elseif ($ans->question->type === 'essay') {
            $essayScore += $ans->score ?? 0;
            $totalMarks +=  $ans->score ?? 0; // بافتراض أن الدرجة القصوى للسؤال المقالي هي نفس الدرجة المعطاة
        }
    }

    $finalScore = $mcqScore + $essayScore;

    // ✅ تحديث أو إنشاء ExamResult
    $examResult = ExamResult::updateOrCreate(
        [
            'exam_id' => $examId,
            'student_id' => $studentId
        ],
        [
            'score' => $finalScore,
            'total_marks' => $totalMarks,
        ]
    );

    return response()->json([
        'status' => 200,
        'message' => 'Score updated and result saved',
        'data' => [
            'answer' => $answer,
            'exam_result' => $examResult
        ]
    ]);
}

}
