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
        return response()->json([
            'status' => 200,
            'data' => CourseEnrollmentResource::collection($enrollments),
            'pagination' => [
                'current_page' => $enrollments->currentPage(),
                'last_page' => $enrollments->lastPage(),
                'total' => $enrollments->total(),
            ]
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:students,id',
        'course_id' => 'required|exists:courses,id'
    ]);

    // تحقق اذا الطالب طلب التسجيل من قبل
    $exists = CourseEnrollment::where('student_id', $request->student_id)
                               ->where('course_id', $request->course_id)
                               ->first();
    if ($exists) {
        return response()->json(['message' => 'Enrollment request already exists'], 422);
    }

    $enrollment = CourseEnrollment::create([
        'student_id' => $request->student_id,
        'course_id' => $request->course_id,
        'status' => 'pending',  // default pending
    ]);

    return response()->json([
        'status' => 201,
        'message' => 'Enrollment request submitted successfully',
        'data' => new CourseEnrollmentResource($enrollment)
    ], 201)->header('Access-Control-Allow-Origin', '*')
           ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
           ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');;
}


    public function show(CourseEnrollment $enrollment)
    {
        return response()->json([
            'status' => 200,
            'data' => new CourseEnrollmentResource($enrollment)
        ]);
    }

    public function destroy(CourseEnrollment $enrollment)
    {
        $enrollment->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Enrollment deleted successfully'
        ]);
    }
    public function getByStudent($student_id)
    {
        $enrollments = CourseEnrollment::with('course')
            ->where('student_id', $student_id)->where('status', 'approved')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $enrollments
        ])->header('Access-Control-Allow-Origin', '*')
           ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
           ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

}
