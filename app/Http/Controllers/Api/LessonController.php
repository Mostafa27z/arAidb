<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function getLessonsByTeacher($teacherId)
{
    // نجيب كل الكورسات التي تخص هذا المدرس
    $courseIds = \App\Models\Course::where('teacher_id', $teacherId)->pluck('id');

    // نجيب كل الدروس التابعة لهذه الكورسات
    $lessons = \App\Models\Lesson::whereIn('course_id', $courseIds)
                ->whereNull('deleted_at')  // لو عندك soft delete
                ->get();

    return response()->json([
        'status' => 200,
        'data' => \App\Http\Resources\LessonResource::collection($lessons),
    ]);
}

    public function index()
    {
        $lessons = Lesson::whereNull('deleted_at')->paginate(10);

        return response()->json([
            'status'     => 200,
            'data'       => LessonResource::collection($lessons),
            'pagination' => [
                'current_page' => $lessons->currentPage(),
                'last_page'    => $lessons->lastPage(),
                'total'        => $lessons->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'attachment' => 'nullable|file'
        ]);

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('lesson_attachments', 'public');
        }

        $lesson = Lesson::create([
            'course_id' => $request->course_id,
            'title'     => $request->title,
            'content'   => $request->content,
            'attachment' => $attachmentPath ? Storage::url($attachmentPath) : null,
        ]);

        return response()->json([
            'status' => 201,
            'data' => new LessonResource($lesson),
        ], 201);
    }

    public function show(Lesson $lesson)
    {
        if ($lesson->deleted_at) {
            return response()->json(['message' => 'Lesson not found'], 404);
        }

        return response()->json([
            'status' => 200,
            'data'   => new LessonResource($lesson),
        ], 200);
    }

    public function update(Request $request, Lesson $lesson)
    {
        $validatedData = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title'     => ['required', 'string', 'max:255'],
            'content'   => ['required', 'string'],
            'attachment' => 'nullable|file'
        ]);

        if ($request->hasFile('attachment')) {
            if ($lesson->attachment) {
                $oldPath = str_replace('/storage/', '', $lesson->attachment);
                Storage::disk('public')->delete($oldPath);
            }
            $attachmentPath = $request->file('attachment')->store('lesson_attachments', 'public');
            $lesson->attachment = Storage::url($attachmentPath);
        }

        $lesson->update([
            'course_id' => $validatedData['course_id'],
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'attachment' => $lesson->attachment,
        ]);

        return response()->json([
            'status' => 200,
            'data'   => new LessonResource($lesson),
        ], 200);
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();

        return response()->json([
            'status'  => 200,
            'message' => 'The lesson was successfully soft deleted',
        ], 200);
    }

    public function getLessonsByCourseId($courseId)
    {
        $lessons = Lesson::where('course_id', $courseId)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 200,
            'data'   => LessonResource::collection($lessons),
        ]);
    }

    public function getNextLesson(Lesson $lesson)
    {
        $nextLesson = Lesson::where('course_id', $lesson->course_id)
            ->where('created_at', '>', $lesson->created_at)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'asc')
            ->first();

        return response()->json([
            'status' => 200,
            'data'   => $nextLesson ? new LessonResource($nextLesson) : null,
        ]);
    }

    public function getPreviousLesson(Lesson $lesson)
    {
        $previousLesson = Lesson::where('course_id', $lesson->course_id)
            ->where('created_at', '<', $lesson->created_at)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'status' => 200,
            'data'   => $previousLesson ? new LessonResource($previousLesson) : null,
        ]);
    }

    public function getLatestLessons()
    {
        $lessons = Lesson::whereNull('deleted_at')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'status' => 200,
            'data'   => LessonResource::collection($lessons),
        ]);
    }
}
