<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeacherAssignmentReviewResource;
use App\Models\TeacherAssignmentReview;
use Illuminate\Http\Request;

class TeacherAssignmentReviewController extends Controller
{
    /**
     * عرض جميع التقييمات
     */
    public function index()
    {
        $reviews = TeacherAssignmentReview::with(['submission', 'teacher'])->latest()->paginate(10);

        return response()->json([
            'status' => 200,
            'data' => TeacherAssignmentReviewResource::collection($reviews),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
            ]
        ]);
    }

    /**
     * إضافة تقييم جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'submission_id' => 'required|exists:student_submissions,id',
            'teacher_id' => 'required|exists:teachers,id',
            'feedback' => 'required|string',
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $review = TeacherAssignmentReview::create($request->all());

        return response()->json([
            'status' => 201,
            'data' => new TeacherAssignmentReviewResource($review),
        ], 201);
    }

    /**
     * عرض تقييم معين
     */
    public function show(TeacherAssignmentReview $teacherAssignmentReview)
    {
        return response()->json([
            'status' => 200,
            'data' => new TeacherAssignmentReviewResource($teacherAssignmentReview),
        ], 200);
    }

    /**
     * تعديل تقييم
     */
    public function update(Request $request, TeacherAssignmentReview $teacherAssignmentReview)
    {
        $request->validate([
            'feedback' => 'sometimes|string',
            'score' => 'sometimes|numeric|min:0|max:100',
        ]);

        $teacherAssignmentReview->update($request->only('feedback', 'score'));

        return response()->json([
            'status' => 200,
            'message' => 'Updated',
            'data' => new TeacherAssignmentReviewResource($teacherAssignmentReview),
        ], 200);
    }

    /**
     * حذف تقييم
     */
    public function destroy(TeacherAssignmentReview $teacherAssignmentReview)
    {
        $teacherAssignmentReview->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Deleted.',
        ], 200);
    }

    public function getReviewsByTeacherId($teacherId)
    {
        $reviews = TeacherAssignmentReview::where('teacher_id', $teacherId)->get();
        return response()->json($reviews);
    }

    public function getReviewsByStudentId($studentId)
    {
        $reviews = TeacherAssignmentReview::whereHas('submission', function ($query) use ($studentId) {
        $query->where('student_id', $studentId);
    })->with(['submission', 'teacher'])->get();

    return response()->json([
        'status' => 200,
        'data' => TeacherAssignmentReviewResource::collection($reviews)
    ]);
    }

    public function getReviewsByCourseId($courseId)
{
    $reviews = TeacherAssignmentReview::whereHas('submission.assignment.course', function ($query) use ($courseId) {
        $query->where('id', $courseId);
    })->with(['submission', 'teacher'])->get();

    return response()->json([
        'status' => 200,
        'data' => TeacherAssignmentReviewResource::collection($reviews)
    ]);
}

    public function getReviewsByAssignmentId($assignmentId)
{
    $reviews = TeacherAssignmentReview::whereHas('submission.assignment', function ($query) use ($assignmentId) {
        $query->where('id', $assignmentId);
    })->with(['submission', 'teacher'])->get();

    return response()->json([
        'status' => 200,
        'data' => TeacherAssignmentReviewResource::collection($reviews)
    ]);
}


    public function getReviewsBySubmissionId($submissionId)
    {
        $reviews = TeacherAssignmentReview::where('submission_id', $submissionId)->get();
        return response()->json($reviews);
    }

    public function storeOrUpdate(Request $request)
{
    $request->validate([
        'submission_id' => 'required|exists:student_submissions,id',
        'teacher_id' => 'required|exists:teachers,id',
        'feedback' => 'required|string',
        'score' => 'required|numeric|min:0|max:100',
    ]);

    // نحاول نجيب لو في ريفيو موجود أصلاً
    $review = TeacherAssignmentReview::where('submission_id', $request->submission_id)
        ->where('teacher_id', $request->teacher_id)
        ->first();

    if ($review) {
        // تحديث القديم
        $review->update([
            'feedback' => $request->feedback,
            'score' => $request->score,
        ]);

        $message = 'Review updated successfully';
    } else {
        // إنشاء جديد
        $review = TeacherAssignmentReview::create($request->all());
        $message = 'Review created successfully';
    }

    return response()->json([
        'status' => 200,
        'message' => $message,
        'data' => new TeacherAssignmentReviewResource($review),
    ], 200);
}

}
