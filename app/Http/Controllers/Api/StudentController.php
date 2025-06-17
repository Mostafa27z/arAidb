<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\CourseEnrollment;
use App\Models\LessonProgress;
use App\Rules\UserRoleValidation;
use Illuminate\Http\Request;
use App\Rules\ParentRole;
use Illuminate\Validation\Rule;
use App\Models\User;

class StudentController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $query = Student::with('user');

    // إضافة البحث
    if ($request->has('search') && $request->search != '') {
        $searchTerm = $request->search;

        $query->whereHas('user', function($q) use ($searchTerm) {
            $q->where('name', 'like', "%{$searchTerm}%")
              ->orWhere('email', 'like', "%{$searchTerm}%");
        });
    }

    $students = $query->paginate(10);

    return response()->json([
        'status' => 200,
        'data' => StudentResource::collection($students),
        'pagination' => [
            'current_page' => $students->currentPage(),
            'last_page' => $students->lastPage(),
            'total' => $students->total(),
        ]
    ], 200);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'student_id' => ['required', 'exists:users,id', 'unique:students,student_id', new UserRoleValidation('student')],
            'parent_id' => ['nullable', 'exists:users,id', new UserRoleValidation('parent')],
            'grade_level' => 'required|string|max:50'
        ]);
        // Store
        $student = Student::create([
            'student_id' => $request->student_id,
            'parent_id' => $request->parent_id,
            'grade_level' => $request->grade_level
        ]);
        // Redirect
        return response()->json([
            'status' => 201,
            'data' => new StudentResource($student)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        return response()->json([
            'status' => 200,
            'data' => new StudentResource($student),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        // Validation
        $validatedData = $request->validate([
            'student_id' => [
                'required',
                'exists:users,id',
                Rule::unique('students', 'student_id')->ignore($student->id), // Ignore the current student
                new UserRoleValidation('student')
            ],
            'parent_id' => [
                'nullable',
                'exists:users,id',
                new UserRoleValidation('parent')
            ],
            'grade_level' => 'required|string|max:50'
        ]);

        // Update student record
        $student->update($validatedData);

        // Return response
        return response()->json([
            'status' => 200,
            'data' => new StudentResource($student)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
{
    // 1. حذف عضويات النوادي
    $student->clubMembers()->delete();

    // 2. حذف الاشتراكات في الكورسات
    $student->enrollments()->delete();

    // 3. حذف تقدم الدروس
    $student->progress()->delete();

    // 4. حذف التسليمات (submissions)
    $student->submissions()->delete();

    // 5. حذف الطالب نفسه
    $student->delete();

    // 6. حذف اليوزر المرتبط بالطالب
    if ($student->user) {
        $student->user->delete();
    }

    return response()->json([
        'status' => 200,
        'message' => 'The student and corresponding user were successfully deleted'
    ], 200);
}


    /**
     * Get student profile with user data
     */
    public function profile(Student $student)
    {
        $student->load('user');

        return response()->json([
            'status' => 200,
            'data' => new StudentResource($student)
        ], 200);
    }

    /**
     * Get courses the student is enrolled in
     */
    public function courses(Student $student)
    {
        $student->load('enrollments.course.teacher.user');

        $courses = $student->enrollments->map(function($enrollment) {
            return $enrollment->course;
        });

        return response()->json([
            'status' => 200,
            'data' => $courses
        ], 200);
    }

    /**
     * Enroll student in a course
     */
    public function enroll(Request $request, Student $student, Course $course)
    {
        // Check if already enrolled
        $existing = CourseEnrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 422,
                'message' => 'Student already enrolled in this course'
            ], 422);
        }

        // Create enrollment
        $enrollment = CourseEnrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Successfully enrolled',
            'data' => $enrollment
        ], 201);
    }

    /**
     * Get lesson progress for a student
     */
    public function lessonProgress(Student $student, Lesson $lesson)
    {
        $progress = LessonProgress::firstOrCreate(
            [
                'student_id' => $student->id,
                'lesson_id' => $lesson->id
            ],
            [
                'progress_percentage' => 0,
                'status' => 'not_started'
            ]
        );

        return response()->json([
            'status' => 200,
            'data' => $progress
        ], 200);
    }

    /**
     * Update lesson progress
     */
    public function updateProgress(Request $request, Student $student, Lesson $lesson)
    {
        $request->validate([
            'progress_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:not_started,in_progress,completed',
        ]);

        $progress = LessonProgress::updateOrCreate(
            [
                'student_id' => $student->id,
                'lesson_id' => $lesson->id
            ],
            [
                'progress_percentage' => $request->progress_percentage,
                'status' => $request->status,
                'completed_at' => $request->status === 'completed' ? now() : null,
                'last_accessed' => now()
            ]
        );

        return response()->json([
            'status' => 200,
            'data' => $progress
        ], 200);
    }

    /**
     * Unenroll student from a course
     */
    public function unenroll(Student $student, Course $course)
    {
        $enrollment = CourseEnrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'status' => 404,
                'message' => 'Student is not enrolled in this course'
            ], 404);
        }

        $enrollment->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Successfully unenrolled from course'
        ], 200);
    }

    /**
     * Get all progress across all lessons for a student
     */
    public function allProgress(Student $student)
    {
        $progress = LessonProgress::where('student_id', $student->id)
            ->with('lesson.course')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $progress
        ], 200);
    }
    /**
 * Get all students for a specific parent
 */
public function getByParent($parentId)
{
    $students = Student::where('parent_id', $parentId)->with('user')->get();

    return response()->json([
        'status' => 200,
        'data' => StudentResource::collection($students)
    ], 200);
}
public function attachStudentToParent(Request $request)
{
    $parentId = auth()->user()->id;

    $validated = $request->validate([
        'email' => 'required|email|exists:users,email'
    ]);

    $user = User::where('email', $validated['email'])
                ->where('role', 'student')
                ->first();

    if (!$user) {
        return response()->json([
            'status' => 404,
            'message' => 'Student not found'
        ], 404);
    }

    $student = Student::where('student_id', $user->id)->first();

    if (!$student) {
        return response()->json([
            'status' => 404,
            'message' => 'Student record not found'
        ], 404);
    }

    if ($student->parent_id) {
        return response()->json([
            'status' => 400,
            'message' => 'Student already linked to a parent'
        ], 400);
    }

    $student->update([
        'parent_id' => $parentId
    ]);

    return response()->json([
        'status' => 200,
        'message' => 'Student successfully linked to your account',
        'data' => new StudentResource($student)
    ]);
}
public function assignToParent(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'parent_id' => 'required|exists:users,id'
    ]);

    $studentUser = User::where('email', $request->email)->where('role', 'student')->first();
    if (!$studentUser) {
        return response()->json(['message' => 'هذا البريد لا يعود لطالب'], 404);
    }

    $student = Student::where('student_id', $studentUser->id)->first();
    if (!$student) {
        return response()->json(['message' => 'الطالب غير موجود في قاعدة البيانات'], 404);
    }

    $student->parent_id = $request->parent_id;
    $student->save();

    return response()->json(['message' => 'تم ربط الطالب بنجاح'], 200);
}


}