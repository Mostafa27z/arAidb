<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseEnrollmentResource;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;

class CourseEnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = CourseEnrollment::paginate(10);

        return $this->successResponse(
            CourseEnrollmentResource::collection($enrollments),
            [
                'current_page' => $enrollments->currentPage(),
                'last_page' => $enrollments->lastPage(),
                'total' => $enrollments->total(),
            ]
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id'
        ]);

        $exists = CourseEnrollment::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->first();

        if ($exists) {
            return $this->errorResponse('Enrollment request already exists', 422);
        }

        $enrollment = CourseEnrollment::create([
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'status' => 'pending',
        ]);

        return $this->successResponse(new CourseEnrollmentResource($enrollment), null, 201);
    }

    public function show(CourseEnrollment $enrollment)
    {
        return $this->successResponse(new CourseEnrollmentResource($enrollment));
    }

    public function destroy(CourseEnrollment $enrollment)
    {
        $enrollment->delete();

        return $this->successResponse(null, null, 200, 'Enrollment deleted successfully');
    }

    public function getByStudent($student_id)
    {
        $enrollments = CourseEnrollment::with('course')
            ->where('student_id', $student_id)
            ->where('status', 'approved')
            ->whereNull('deleted_at') // optional لو شغلت soft delete
            ->get();

        return $this->successResponse($enrollments);
    }

    /** 
     * ✅ Helper Success Response
     */
    private function successResponse($data, $pagination = null, $status = 200, $message = null)
    {
        $response = [
            'status' => $status,
            'data' => $data,
        ];

        if ($pagination) {
            $response['pagination'] = $pagination;
        }

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, $status)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    /** 
     * ✅ Helper Error Response
     */
    private function errorResponse($message, $status = 400)
    {
        return response()->json([
            'status' => $status,
            'message' => $message
        ], $status)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
    public function getEnrollmentsByTeacher($teacher_id)
{
    // نجيب الكورسات الخاصة بالمدرس
    $courseIds = \App\Models\Course::where('teacher_id', $teacher_id)->pluck('id');

    // نجيب كل الـ enrollments الخاصة بهذه الكورسات مع العلاقات
    $enrollments = \App\Models\CourseEnrollment::with(['student.user', 'course'])
        ->whereIn('course_id', $courseIds)
        ->get();

    // نرجع باستخدام الـ resource
    return $this->successResponse(CourseEnrollmentResource::collection($enrollments));
}

public function updateStatus(Request $request, CourseEnrollment $enrollment)
{
    $request->validate([
        'status' => 'required|in:pending,approved,rejected'
    ]);

    $enrollment->status = $request->status;
    $enrollment->save();

    return $this->successResponse(new CourseEnrollmentResource($enrollment));
}

}
