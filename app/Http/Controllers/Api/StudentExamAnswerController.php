<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentExamAnswer;
use Illuminate\Http\Request;

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
                'question_id' => $answer['question_id'],
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
    public function getAnswersForExam($examId, $studentId)
    {
        $answers = StudentExamAnswer::with(['question', 'selectedOption'])
            ->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $answers
        ]);
    }

    // Update score for a specific essay answer
    public function updateScore(Request $request, $id)
    {
        $request->validate([
            'score' => 'required|numeric|min:0'
        ]);

        $answer = StudentExamAnswer::findOrFail($id);
        $answer->update(['score' => $request->score]);

        return response()->json([
            'status' => 200,
            'message' => 'Score updated',
            'data' => $answer
        ]);
    }
}
