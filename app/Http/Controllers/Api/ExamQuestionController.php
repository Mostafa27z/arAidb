<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamQuestion;
use App\Models\ExamQuestionOption;
use Illuminate\Http\Request;

class ExamQuestionController extends Controller
{
    // جلب جميع الأسئلة المرتبطة بامتحان معين
    public function index($examId)
    {
        $questions = ExamQuestion::with('options')->where('exam_id', $examId)->get();
        return response()->json(['status' => 200, 'data' => $questions]);
    }

    // إنشاء سؤال جديد
   public function store(Request $request)
{
    $validated = $request->validate([
        'exam_id' => 'required|exists:exams,id',
        'question_text' => 'required|string',
        'type' => 'required|in:mcq,essay',
        'options' => 'required_if:type,mcq|array',
        'options.*.option_text' => 'required|string',
        'options.*.is_correct' => 'required|boolean',
    ]);

    if ($request->type === 'mcq') {
        $correctCount = collect($request->options)->where('is_correct', true)->count();
        if ($correctCount !== 1) {
            return response()->json([
                'message' => 'يجب اختيار إجابة صحيحة واحدة فقط.'
            ], 422);
        }
    }

    $question = ExamQuestion::create($request->only('exam_id', 'question_text', 'type'));

    if ($request->type === 'mcq') {
        foreach ($request->options as $opt) {
            $question->options()->create($opt);
        }
    }

    return response()->json(['status' => 201, 'data' => $question->load('options')]);
}
public function update(Request $request, $id)
{
    $question = ExamQuestion::findOrFail($id);

    $validated = $request->validate([
        'question_text' => 'required|string',
        'type' => 'required|in:mcq,essay',
        'options' => 'required_if:type,mcq|array',
        'options.*.option_text' => 'required|string',
        'options.*.is_correct' => 'required|boolean',
    ]);

    if ($request->type === 'mcq') {
        $correctCount = collect($request->options)->where('is_correct', true)->count();
        if ($correctCount !== 1) {
            return response()->json([
                'message' => 'يجب اختيار إجابة صحيحة واحدة فقط.'
            ], 422);
        }
    }

    $question->update($request->only('question_text', 'type'));

    // إعادة تعيين الاختيارات إن كانت MCQ
    $question->options()->delete();
    if ($request->type === 'mcq') {
        foreach ($request->options as $opt) {
            $question->options()->create($opt);
        }
    }

    return response()->json(['status' => 200, 'data' => $question->load('options')]);
}

    // حذف سؤال
    public function destroy($id)
    {
        $question = ExamQuestion::findOrFail($id);
        $question->delete();
        return response()->json(['status' => 200, 'message' => 'تم حذف السؤال']);
    }
}
