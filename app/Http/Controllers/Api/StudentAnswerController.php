<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentAnswerResource;
use App\Models\StudentAnswer;
use App\Models\Question;
use Illuminate\Http\Request;

class StudentAnswerController extends Controller
{
    /**
     * Display a listing of student answers.
     */
    public function index()
    {
        $answers = StudentAnswer::with(['student', 'question', 'selectedOption'])->latest()->paginate(10);

        return response()->json([
            'status' => 200,
            'data' => StudentAnswerResource::collection($answers),
            'pagination' => [
                'current_page' => $answers->currentPage(),
                'last_page' => $answers->lastPage(),
                'total' => $answers->total(),
            ]
        ]);
    }
    public function getAnswersForLesson($studentId, $lessonId)
{
    $answers = \App\Models\StudentAnswer::where('student_id', $studentId)
        ->whereHas('question', function ($query) use ($lessonId) {
            $query->where('lesson_id', $lessonId);
        })
        ->get();

    return response()->json([
        'status' => 200,
        'data' => $answers
    ]);
}


    /**
     * Store a newly created student answer.
     */
    public function store(Request $request)
{
    // ... تخزين إجابة الطالب

    $studentId = $request->student_id;
    $lessonId = $request->lesson_id;

    // تحقّق هل أجاب على كل أسئلة الدرس
    $totalQuestions = Question::where('lesson_id', $lessonId)->count();
    $answered = StudentAnswer::where('student_id', $studentId)
                              ->whereHas('question', fn($q) => $q->where('lesson_id', $lessonId))
                              ->count();

    if ($totalQuestions > 0 && $answered >= $totalQuestions) {
        $progress = LessonProgress::firstOrNew([
            'student_id' => $studentId,
            'lesson_id' => $lessonId,
        ]);

        $progress->progress_percentage = 100;
        $progress->status = 'completed';
        $progress->completed_at = now();
        $progress->save();
    }

    return response()->json([
        'status' => 201,
        'message' => 'Answer saved successfully',
    ]);
}


    /**
     * Display the specified student answer.
     */
    public function show(StudentAnswer $answer)
    {
        return response()->json([
            'status' => 200,
            'data' => new StudentAnswerResource($answer),
        ], 200);
    }

    /**
     * Update the specified student answer.
     */
    public function update(Request $request, StudentAnswer $answer)
    {
        $request->validate([
            'selected_option_id' => 'nullable|exists:question_options,id',
            'essay_answer' => 'nullable|string',
            'is_correct' => 'nullable|boolean',
        ]);

        $answer->update($request->only('selected_option_id', 'essay_answer', 'is_correct'));

        return response()->json([
            'status' => 200,
            'message' => 'Answer updated successfully.',
            'data' => new StudentAnswerResource($answer),
        ], 200);
    }

    /**
     * Delete the specified student answer.
     */
    public function destroy(StudentAnswer $answer)
    {
        $answer->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Answer deleted successfully.',
        ], 200);
    }
}
