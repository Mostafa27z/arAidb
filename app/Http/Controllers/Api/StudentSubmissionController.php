<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentSubmissionResource;
use App\Models\Assignment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $submissions = StudentSubmission::with(['student.user', 'assignment'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 200,
            'data' => StudentSubmissionResource::collection($submissions),
            'pagination' => [
                'current_page' => $submissions->currentPage(),
                'last_page' => $submissions->lastPage(),
                'total' => $submissions->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     // Validation
    //     $request->validate([
    //         'student_id' => 'required|exists:students,id',
    //         'assignment_id' => 'required|exists:assignments,id',
    //         'submission_text' => 'required_without:file_path|string|nullable',
    //         'file_path' => 'required_without:submission_text|file|max:10240|nullable', // 10MB max
    //         // 'notes' => 'nullable|string',
    //         // 'status' => 'required|in:submitted,draft'
    //     ]);

    //     // Check if assignment exists and is not past due date
    //     $assignment = Assignment::findOrFail($request->assignment_id);
    //     if (now() > $assignment->due_date) {
    //         return response()->json([
    //             'status' => 422,
    //             'message' => 'Assignment submission deadline has passed'
    //         ], 422);
    //     }

    //     // Check if student has already submitted this assignment
    //     $existingSubmission = StudentSubmission::where('student_id', $request->student_id)
    //         ->where('assignment_id', $request->assignment_id)
    //         ->first();

    //     if ($existingSubmission) {
    //         return response()->json([
    //             'status' => 422,
    //             'message' => 'You have already submitted this assignment',
    //             'data' => new StudentSubmissionResource($existingSubmission)
    //         ], 422);
    //     }

    //     // Handle file upload if present
    //     $filePath = null;
    //     if ($request->hasFile('file_path')) {
    //         $filePath = $request->file('file_path')->store('submissions', 'public');
    //     }

    //     // Create submission
    //     $submission = StudentSubmission::create([
    //         'student_id' => $request->student_id,
    //         'assignment_id' => $request->assignment_id,
    //         'submission_text' => $request->submission_text,
    //         'file_path' => $filePath ? Storage::url($filePath) : null,
    //         // 'notes' => $request->notes,
    //         // 'status' => $request->status,
    //         'submission_date' => now(),
    //     ]);

    //     // Load relationships
    //     $submission->load(['student.user', 'assignment']);

    //     return response()->json([
    //         'status' => 201,
    //         'message' => 'Assignment submitted successfully',
    //         'data' => new StudentSubmissionResource($submission)
    //     ], 201);
    // }
public function store(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:students,id',
        'assignment_id' => 'required|exists:assignments,id',
        'submission_text' => 'required_without:file_path|string|nullable',
        'file_path' => 'required_without:submission_text|file|max:10240|nullable',
    ]);

    $assignment = Assignment::findOrFail($request->assignment_id);
    if (now() > $assignment->due_date) {
        return response()->json(['message' => 'Assignment submission deadline has passed'], 422);
    }

    $existingSubmission = StudentSubmission::where('student_id', $request->student_id)
        ->where('assignment_id', $request->assignment_id)
        ->first();

    if ($existingSubmission) {
        return response()->json(['message' => 'You have already submitted this assignment'], 422);
    }

    $filePath = null;
    if ($request->hasFile('file_path')) {
        $filePath = $request->file('file_path')->store('submissions', 'public');
    }

    $submission = StudentSubmission::create([
        'student_id' => $request->student_id,
        'assignment_id' => $request->assignment_id,
        'submission_text' => $request->submission_text,
        'file_path' => $filePath ? Storage::url($filePath) : null,
        'submission_date' => now(),
    ]);

    return response()->json(['message' => 'Assignment submitted successfully', 'data' => $submission], 201);
}

public function update(Request $request, StudentSubmission $submission)
{
    $request->validate([
        'submission_text' => 'nullable|string',
        'file_path' => 'sometimes|file|max:10240',
    ]);

    if ($request->hasFile('file_path')) {
        if ($submission->file_path) {
            $oldPath = str_replace('/storage/', '', $submission->file_path);
            Storage::disk('public')->delete($oldPath);
        }
        $filePath = $request->file('file_path')->store('submissions', 'public');
        $submission->file_path = Storage::url($filePath);
    }

    $submission->submission_text = $request->input('submission_text', $submission->submission_text);
    $submission->save();

    return response()->json(['message' => 'Submission updated successfully', 'data' => $submission], 200);
}

    /**
     * Display the specified resource.
     */
    public function show(StudentSubmission $studentSubmission)
    {
        $studentSubmission->load(['student.user', 'assignment', 'reviews.teacher.user']);

        return response()->json([
            'status' => 200,
            'data' => new StudentSubmissionResource($studentSubmission),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
//    public function update(Request $request, StudentSubmission $studentSubmission)
// {
//     // Validation
//     $request->validate([
//         'submission_text' => 'nullable|string',
//         'file_path' => 'sometimes|file|max:10240',
//     ]);

//     if ($request->hasFile('file_path')) {
//         // حذف القديم لو وجد
//         if ($studentSubmission->file_path) {
//             $oldPath = str_replace('/storage/', '', $studentSubmission->file_path);
//             Storage::disk('public')->delete($oldPath);
//         }

//         $filePath = $request->file('file_path')->store('submissions', 'public');
//         $studentSubmission->file_path = Storage::url($filePath);
//     }

//     $studentSubmission->submission_text = $request->input('submission_text', $studentSubmission->submission_text);
//     $studentSubmission->updated_at = now();
//     $studentSubmission->save();

//     return response()->json([
//         'status' => 200,
//         'message' => 'Submission updated successfully',
//         'data' => new StudentSubmissionResource($studentSubmission)
//     ], 200);
// }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentSubmission $studentSubmission)
    {
        // Check if submission can be deleted (only if not yet reviewed)
        if ($studentSubmission->reviews->count() > 0) {
            return response()->json([
                'status' => 422,
                'message' => 'Cannot delete submission after it has been reviewed'
            ], 422);
        }

        // Delete file if exists
        if ($studentSubmission->file_path) {
            $path = str_replace('/storage/', '', $studentSubmission->file_path);
            Storage::disk('public')->delete($path);
        }

        $studentSubmission->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Submission was successfully deleted'
        ], 200);
    }

    /**
     * Get all submissions for a specific assignment
     */
    public function assignmentSubmissions(Assignment $assignment)
    {
        $submissions = StudentSubmission::where('assignment_id', $assignment->id)
            ->with(['student.user', 'reviews'])
            ->paginate(10);

        return response()->json([
            'status' => 200,
            'data' => StudentSubmissionResource::collection($submissions),
            'pagination' => [
                'current_page' => $submissions->currentPage(),
                'last_page' => $submissions->lastPage(),
                'total' => $submissions->total(),
            ]
        ]);
    }

    /**
     * Get all submissions by a specific student
     */
    public function studentSubmissions(Student $student)
    {
        $submissions = StudentSubmission::where('student_id', $student->id)
            ->with(['assignment.course', 'reviews'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 200,
            'data' => StudentSubmissionResource::collection($submissions),
            'pagination' => [
                'current_page' => $submissions->currentPage(),
                'last_page' => $submissions->lastPage(),
                'total' => $submissions->total(),
            ]
        ]);
    }
    public function getSubmissionsByStudent(Student $student)
    {
        $submissions = StudentSubmission::where('student_id', $student->id)
                        ->with('assignment')
                        ->get();

        return response()->json(['status' => 200, 'data' => $submissions]);
    }
    public function getSubmissionsByAssignment(Assignment $assignment)
    {
        $submissions = StudentSubmission::where('assignment_id', $assignment->id)
                        ->with('student')
                        ->get();

        return response()->json(['status' => 200, 'data' => $submissions]);
    }

public function checkSubmissionStatus(Request $request)
{
    $data = $request->validate([
        'student_id' => 'required|exists:students,id',
        'assignment_id' => 'required|exists:assignments,id',
    ]);

    $submission = StudentSubmission::where('student_id', $data['student_id'])
        ->where('assignment_id', $data['assignment_id'])
        ->first();

    if ($submission) {
        return response()->json([
            'status' => 200,
            'submitted' => true,
            'submission_id' => $submission->id,
            'submitted_at' => $submission->updated_at,
        ]);
    } else {
        return response()->json([
            'status' => 200,
            'submitted' => false,
        ]);
    }
}
public function getSubmissionsByTeacher($teacherId)  
{  
    $submissions = StudentSubmission::whereHas('assignment.lesson.course', function ($q) use ($teacherId) {  
            $q->where('teacher_id', $teacherId);  
        })  
        ->with(['student.user', 'assignment.lesson.course', 'reviews.teacher.user'])  
        ->get(); 
 
    $mappedSubmissions = $submissions->map(function ($submission) { 
        return [ 
            'id' => $submission->id, 
            'student_id' => $submission->student_id, 
            'student_name' => optional(optional($submission->student)->user)->name ?? 'N/A', 
            'assignment_id' => $submission->assignment_id, 
            'assignment_title' => optional($submission->assignment)->title ?? 'N/A', 
            'submission_date' => $submission->submission_date, 
            'file_path' => $submission->file_path, 
            'submission_text' => $submission->submission_text, 
            'created_at' => $submission->created_at->toDateTimeString(), 
            'updated_at' => $submission->updated_at->toDateTimeString(), 
            
            // نضيف بيانات الريفيو 
            'reviews' => $submission->reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'teacher_id' => $review->teacher_id,
                    'teacher_name' => optional(optional($review->teacher)->user)->name ?? 'N/A',
                    'feedback' => $review->feedback,
                    'score' => $review->score,
                    'created_at' => $review->created_at->toDateTimeString(),
                ];
            }),
        ]; 
    }); 
 
    return response()->json([  
        'status' => 200,  
        'data' => $mappedSubmissions  
    ]);  
}


}
