<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    // ✅ جلب كل الكورسات (مع soft delete)
    public function index()
    {
        $courses = Course::with(['teacher.user', 'enrollments'])
            ->whereNull('deleted_at')
            ->get();

        $coursesData = $courses->map(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'teacher' => $course->teacher->user->name,
                'students_count' => $course->enrollments->count(),
                'thumbnail' => $course->thumbnail ? asset($course->thumbnail) : null
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $coursesData
        ]);
    }

    // ✅ عرض تفاصيل كورس
    public function show(Course $course)
    {
        $course->load(['teacher.user', 'lessons']);

        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    // ✅ جلب الدروس داخل كورس
    public function lessons(Course $course)
    {
        $lessons = $course->lessons;

        return response()->json([
            'status' => 'success',
            'data' => $lessons
        ]);
    }

    // ✅ إنشاء كورس جديد
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:teachers,id',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120'
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'teacher_id' => $request->teacher_id,
            'thumbnail' => $thumbnailPath ? Storage::url($thumbnailPath) : null
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $course
        ], 201);
    }

    // ✅ تحديث كورس
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'sometimes|exists:teachers,id',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120'
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                $oldPath = str_replace('/storage/', '', $course->thumbnail);
                Storage::disk('public')->delete($oldPath);
            }

            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $course->thumbnail = Storage::url($thumbnailPath);
        }

        $course->fill($request->only(['title', 'description', 'teacher_id']))->save();

        return response()->json([
            'status' => 'success',
            'data' => $course
        ], 200);
    }

    // ✅ جلب كورسات معينه لمعلم
    public function getCoursesByTeacher($teacherId)
    {
        $teacher = Teacher::findOrFail($teacherId);

        $courses = Course::with('teacher.user')
            ->withCount('approvedEnrollments')
            ->where('teacher_id', $teacherId)
            ->whereNull('deleted_at')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $courses
        ]);
    }

    // ✅ حذف (soft delete مع العلاقات)
    public function destroy(Course $course)
{
    // حذف الصورة لو فيه
    if ($course->thumbnail) {
        $path = str_replace('/storage/', '', $course->thumbnail);
        Storage::disk('public')->delete($path);
    }

    // حذف enrollments
    $course->enrollments()->delete();

    // حذف الدروس ثم جميع الواجبات الخاصة بها
    $lessons = $course->lessons()->get();
    foreach ($lessons as $lesson) {
        $lesson->assignments()->delete();
        $lesson->delete();
    }

    // حذف الكورس نفسه (soft delete)
    $course->delete();

    return response()->json([
        'status' => 200,
        'message' => 'Course was soft deleted successfully'
    ]);
}

}
