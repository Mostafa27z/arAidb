<?php

use App\Http\Controllers\Api\AIChatLogController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ExamResultController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\LessonProgressController;
use App\Http\Controllers\Api\ParentTeacherConversationController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\QuestionOptionController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherAssignmentReviewController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentSubmissionController;
use App\Http\Controllers\Api\ClubController;
use App\Http\Controllers\Api\ClubMemberController;
use App\Http\Controllers\Api\ClubChatMessageController;
use App\Http\Controllers\Api\StudentAnswerController;
use App\Http\Controllers\Api\CourseEnrollmentController;
use App\Http\Controllers\Api\StudentExamAnswerController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\GroupSessionController;
use App\Http\Controllers\Api\GroupMemberController;
Route::get('/', function () {
    return response()->json(['message' => 'Welcome to API'], 200);
});


// Route::prefix('clubs')->group(function () {
//     // Chat Messages for a specific club
//     Route::get('/{clubId}/messages', [ClubChatMessageController::class, 'index']); // Get all messages
//     Route::post('/messages', [ClubChatMessageController::class, 'store']);          // Send message
//     Route::get('/messages/{clubChatMessage}', [ClubChatMessageController::class, 'show']); // Get single message
//     Route::delete('/messages/{clubChatMessage}', [ClubChatMessageController::class, 'destroy']); // Delete message
//     // Clubs routes
//     Route::get('/', [ClubController::class, 'index']); // Get all clubs
//     Route::post('/', [ClubController::class, 'store']); // Create club
//     Route::get('/{club}', [ClubController::class, 'show']); // Get club details
//     Route::put('/{club}', [ClubController::class, 'update']); // Update club
//     Route::delete('/{club}', [ClubController::class, 'destroy']); // Delete club

//     // Club Members routes
//     Route::prefix('members')->group(function () {
//         Route::get('/', [ClubMemberController::class, 'index']); // Get all club members
//         Route::post('/', [ClubMemberController::class, 'store']); // Add member
//         Route::get('/{clubMember}', [ClubMemberController::class, 'show']); // Get member details
//         Route::delete('/{clubMember}', [ClubMemberController::class, 'destroy']); // Remove member
//     });
// });
Route::prefix('clubs')->group(function () {

    

    // ضع members routes قبل أي route فيه {club}
    Route::prefix('members')->group(function () {
        Route::get('/', [ClubMemberController::class, 'index']);
        Route::post('/', [ClubMemberController::class, 'store']);
        Route::get('/{clubMember}', [ClubMemberController::class, 'show']);
        Route::delete('/{clubMember}', [ClubMemberController::class, 'destroy']);
    });

    // Chat routes بعد الكل
    Route::get('/{clubId}/messages', [ClubChatMessageController::class, 'index']);
    Route::post('/messages', [ClubChatMessageController::class, 'store']);
    Route::get('/messages/{clubChatMessage}', [ClubChatMessageController::class, 'show']);
    Route::delete('/messages/{clubChatMessage}', [ClubChatMessageController::class, 'destroy']);
    Route::get('/student/{studentId}', [ClubController::class, 'getClubsForStudent']);
    Route::get('/teacher/{teacherId}', [ClubController::class, 'getClubsForTeacher']);

    Route::get('/', [ClubController::class, 'index']);  
    Route::post('/', [ClubController::class, 'store']);
    Route::get('/{club}', [ClubController::class, 'show']);
    Route::put('/{club}', [ClubController::class, 'update']);
    Route::delete('/{club}', [ClubController::class, 'destroy']);
});
Route::get('/clubs-with-last-message', [ClubController::class, 'clubsWithLastMessage']);

// Group membership
Route::prefix('group-members')->group(function () {
    Route::post('/request', [GroupMemberController::class, 'requestToJoin']);
    Route::put('/{id}/approve', [GroupMemberController::class, 'approve']);
    Route::put('/{id}/reject', [GroupMemberController::class, 'reject']);
    Route::get('/group/{groupId}/pending', [GroupMemberController::class, 'pendingRequests']);
});

// Group sessions
Route::prefix('group-sessions')->group(function () {
    Route::post('/', [GroupSessionController::class, 'store']);
    Route::get('/group/{groupId}', [GroupSessionController::class, 'getByGroup']);
    Route::get('/by-group/{groupId}', [GroupSessionController::class, 'getByGroup']);
});



Route::prefix('groups')->group(function () {
    Route::get('/', [GroupController::class, 'index']);           // Get all groups (paginated)
    Route::post('/', [GroupController::class, 'store']);          // Create new group
    Route::get('/{group}', [GroupController::class, 'show']);     // Show single group
    Route::put('/{group}', [GroupController::class, 'update']);   // Update group
    Route::delete('/{group}', [GroupController::class, 'destroy']); // Delete group
    Route::get('/student/{studentId}', [GroupController::class, 'getGroupsForStudent']);

});

Route::prefix('submissions')->group(function () {
    Route::post('/check', [StudentSubmissionController::class, 'checkSubmissionStatus']);
    Route::get('/', [StudentSubmissionController::class, 'index']);
    Route::post('/', [StudentSubmissionController::class, 'store']);  // create
    Route::post('/{submission}', [StudentSubmissionController::class, 'update']); // ✅ important: post instead of put
    Route::delete('/{submission}', [StudentSubmissionController::class, 'destroy']);
    Route::get('/student/{student}', [StudentSubmissionController::class, 'getSubmissionsByStudent']);
    Route::get('/assignment/{assignment}', [StudentSubmissionController::class, 'getSubmissionsByAssignment']);
    Route::get('/byteacher/{teacher}', [StudentSubmissionController::class, 'getSubmissionsByTeacher']);

});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Sanctum
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::apiResource('users', UserController::class);
Route::apiResource('courses', CourseController::class);
Route::apiResource('students', StudentController::class);
// Route::get('students/parent/{parentId}', [StudentController::class, 'getByParent']);
Route::prefix('parents')->group(function () {
    Route::get('{parentId}/students', [StudentController::class, 'getByParent']);
});
Route::post('students/assign-to-parent', [StudentController::class, 'assignToParent']);

// Additional student-specific functionalities
Route::prefix('students/{student}')->group(function () {
    // Profile and courses
    Route::get('/profile', [StudentController::class, 'profile']);
    Route::get('/courses', [StudentController::class, 'courses']);

    // Course enrollment
    Route::post('/courses/{course}/enroll', [StudentController::class, 'enroll']);
    Route::delete('/courses/{course}/unenroll', [StudentController::class, 'unenroll']);

    // Lesson progress
    Route::get('/lessons/{lesson}/progress', [StudentController::class, 'lessonProgress']);
    Route::post('/lessons/{lesson}/progress', [StudentController::class, 'updateProgress']);
    Route::get('/progress', [StudentController::class, 'allProgress']);
});

// Teacher Routes
Route::apiResource('teachers', TeacherController::class);
Route::get('courses/teachers/{teacher}/courses', [CourseController::class, 'getCoursesByTeacher']);
Route::prefix('teachers')->group(function () {
    // Additional Teacher Routes
    Route::get('/{teacher}/profile', [TeacherController::class, 'profile']);
    

    Route::get('/{teacher}/assignments', [TeacherController::class, 'assignments']);
    Route::get('/{teacher}/pending-reviews', [TeacherController::class, 'pendingReviews']);
    Route::get('/{teacher}/statistics', [TeacherController::class, 'statistics']);
    Route::post('/{teacher}/assign-course', [TeacherController::class, 'assignCourse']);
    Route::get('/{teacher}/dashboard-summary', [TeacherController::class, 'dashboardSummary']);
    Route::delete('/{teacher}/courses/{course}', [TeacherController::class, 'removeCourse']);
    Route::put('/enrollments/{enrollment}/status', [CourseEnrollmentController::class, 'updateStatus']);
    


});

Route::apiResource('courses', CourseController::class);
Route::get('/courses/{course}/lessons', [CourseController::class, 'lessons']);
Route::prefix('enrollments')->group(function () {
    Route::get('/', [CourseEnrollmentController::class, 'index']);
    Route::post('/', [CourseEnrollmentController::class, 'store']);
    Route::get('/{id}', [CourseEnrollmentController::class, 'show']);
    Route::delete('/{id}', [CourseEnrollmentController::class, 'destroy']);

    // لو عايز تجيب بالطالب (اللي انت عامل لها حالياً getEnrollmentsByStudent)
    Route::get('/student/{student_id}', [CourseEnrollmentController::class, 'getByStudent']);
});
Route::get('/enrollments/teacher/{teacher_id}', [CourseEnrollmentController::class, 'getEnrollmentsByTeacher']);
Route::put('/enrollments/{enrollment}/status', [CourseEnrollmentController::class, 'updateStatus']);
Route::get('/enrollments/all', [CourseEnrollmentController::class, 'getAllEnrollments']);
Route::get('/enrollments/student/{student_id}/all', [CourseEnrollmentController::class, 'getAllEnrollmentsByStudent']);
Route::get('students/{student}/courses-with-progress', [StudentController::class, 'coursesWithProgress']);

// Lesson Routes
Route::prefix('lessons')->group(function () { 
    // Static or fixed routes FIRST
    Route::get('/teacher/{teacherId}', [LessonController::class, 'getLessonsByTeacher']); 
    Route::get('/course/{courseId}/all', [LessonController::class, 'getLessonsByCourseId']); 
    Route::get('/latest/all', [LessonController::class, 'getLatestLessons']); 
    Route::get('/{lesson}/next', [LessonController::class, 'getNextLesson']); 
    Route::get('/{lesson}/previous', [LessonController::class, 'getPreviousLesson']); 

    // Basic CRUD routes AFTER static routes
    Route::get('/', [LessonController::class, 'index']); 
    Route::post('/', [LessonController::class, 'store']); 
    Route::get('/{lesson}', [LessonController::class, 'show']); 
    Route::put('/{lesson}', [LessonController::class, 'update']); 
    Route::delete('/{lesson}', [LessonController::class, 'destroy']); 
});


Route::apiResource('lesson-progress', LessonProgressController::class);
Route::get('/lesson-progress/students/{student}', [LessonProgressController::class, 'getByStudent']);

// Assignment Routes
Route::prefix('assignments')->group(function () {
    Route::get('/', [AssignmentController::class, 'index']);
    Route::post('/', [AssignmentController::class, 'store']);
    Route::get('/{assignment}', [AssignmentController::class, 'show']);
    Route::put('/{assignment}', [AssignmentController::class, 'update']);
    Route::delete('/{assignment}', [AssignmentController::class, 'destroy']);

    // Additional Assignment Routes
    Route::post('/{assignment}/publish', [AssignmentController::class, 'publish']);
    Route::get('/{assignment}/statistics', [AssignmentController::class, 'statistics']);

    // Course Assignments
    Route::get('/course/{course}', [AssignmentController::class, 'courseAssignments']);

    // Teacher Assignments
    Route::get('/teacher/{teacher}', [AssignmentController::class, 'teacherAssignments']);

    // Student Assignments
    Route::get('/student/{student}', [AssignmentController::class, 'studentAssignments']);
});

// AI Chat Log Routes

    // Get all chat logs
    Route::get('/chat-logs', [AIChatLogController::class, 'index']);

    // Send a message and get AI response
    Route::post('/chat-logs', [AIChatLogController::class, 'store']);

    // Get chat history for a specific user
    Route::get('/chat-logs/user/{userId}', [AIChatLogController::class, 'userHistory']);

    // Get a specific chat log
    Route::get('/chat-logs/{aiChatLog}', [AIChatLogController::class, 'show']);

    // Update a specific chat log
    Route::put('/chat-logs/{aiChatLog}', [AIChatLogController::class, 'update']);
    Route::patch('/chat-logs/{aiChatLog}', [AIChatLogController::class, 'update']);

    // Delete a specific chat log
    Route::delete('/chat-logs/{aiChatLog}', [AIChatLogController::class, 'destroy']);


// Teacher Assignment Review Routes
Route::prefix('teacher-assignment-reviews')->group(function () {
    Route::post('/store-or-update', [TeacherAssignmentReviewController::class, 'storeOrUpdate']);

    Route::get('/', [TeacherAssignmentReviewController::class, 'index']);
    // Route::post('/', [TeacherAssignmentReviewController::class, 'store']);
    Route::get('/{teacherAssignmentReview}', [TeacherAssignmentReviewController::class, 'show']);
    Route::put('/{teacherAssignmentReview}', [TeacherAssignmentReviewController::class, 'update']);
    Route::delete('/{teacherAssignmentReview}', [TeacherAssignmentReviewController::class, 'destroy']);

    // Additional filter routes
    Route::get('/by-teacher/{teacherId}', [TeacherAssignmentReviewController::class, 'getReviewsByTeacherId']);
    Route::get('/by-student/{studentId}', [TeacherAssignmentReviewController::class, 'getReviewsByStudentId']);
    Route::get('/by-course/{courseId}', [TeacherAssignmentReviewController::class, 'getReviewsByCourseId']);
    Route::get('/by-assignment/{assignmentId}', [TeacherAssignmentReviewController::class, 'getReviewsByAssignmentId']);
    Route::get('/by-submission/{submissionId}', [TeacherAssignmentReviewController::class, 'getReviewsBySubmissionId']);
});

// Question Routes
Route::prefix('questions')->group(function () {
    // Basic CRUD routes
    Route::get('/', [QuestionController::class, 'index']);
    Route::post('/', [QuestionController::class, 'store']);
    Route::get('/{question}', [QuestionController::class, 'show']);
    Route::put('/{question}', [QuestionController::class, 'update']);
    Route::delete('/{question}', [QuestionController::class, 'destroy']);

    // Filter routes
    Route::get('/by-course/{courseId}', [QuestionController::class, 'getQuestionsByCourseId']);
    Route::get('/by-assignment/{assignmentId}', [QuestionController::class, 'getQuestionsByAssignmentId']);
    Route::get('/by-student/{studentId}', [QuestionController::class, 'getQuestionsByStudentId']);
    Route::get('/by-teacher/{teacherId}', [QuestionController::class, 'getQuestionsByTeacherId']);
    Route::get('/by-lesson/{lessonId}', [QuestionController::class, 'getQuestionsByLessonId']);
});

// Question Options Routes
Route::prefix('question-options')->group(function () {
    // Basic CRUD routes
    Route::get('/', [QuestionOptionController::class, 'index']);
    Route::post('/', [QuestionOptionController::class, 'store']);
    Route::get('/{option}', [QuestionOptionController::class, 'show']);
    Route::put('/{option}', [QuestionOptionController::class, 'update']);
    Route::delete('/{option}', [QuestionOptionController::class, 'destroy']);

    // Additional routes
    Route::get('/question/{questionId}/all', [QuestionOptionController::class, 'getOptionsByQuestionId']);
    Route::get('/question/{questionId}/correct', [QuestionOptionController::class, 'getCorrectOptionsByQuestionId']);
    Route::get('/correct/all', [QuestionOptionController::class, 'getAllCorrectOptions']);
    
});

// Exam Routes
Route::prefix('exams')->group(function () {
    // Basic CRUD routes
    Route::get('/', [ExamController::class, 'index']);
    Route::post('/', [ExamController::class, 'store']);
    Route::get('/{exam}', [ExamController::class, 'show']);
    Route::put('/{exam}', [ExamController::class, 'update']);
    Route::delete('/{exam}', [ExamController::class, 'destroy']);

    // Additional exam filters
    Route::get('/course/{courseId}/all', [ExamController::class, 'getExamsByCourseId']);
    Route::get('/upcoming/all', [ExamController::class, 'getUpcomingExams']);
    Route::get('/past/all', [ExamController::class, 'getPastExams']);
    Route::get('/today/all', [ExamController::class, 'getTodayExams']);
    Route::post('/date-range', [ExamController::class, 'getExamsByDateRange']);

    // Extended functionality
    Route::get('/teacher/{teacherId}/all', [ExamController::class, 'getExamsByTeacher']);
    Route::get('/available/{studentId}', [ExamController::class, 'getAvailableExamsForStudent']);
    Route::get('/pending-grading/{teacherId}', [ExamController::class, 'getExamsNeedingGrading']);
    Route::get('/course/{courseId}/student/{studentId}', [ExamController::class, 'getExamsByCourseAndStudent']);
});


// Exam Results Routes
Route::prefix('exam-results')->group(function () {
    // Basic CRUD routes
    Route::get('/', [ExamResultController::class, 'index']);
    Route::post('/', [ExamResultController::class, 'store']);
    Route::get('/{examResult}', [ExamResultController::class, 'show']);
    Route::put('/{examResult}', [ExamResultController::class, 'update']);
    Route::delete('/{examResult}', [ExamResultController::class, 'destroy']);

    // Student-specific routes
    Route::get('/student/{studentId}/all', [ExamResultController::class, 'getResultsByStudentId']);
    Route::get('/student/{studentId}/performance', [ExamResultController::class, 'getStudentPerformanceSummary']);
    Route::get('/student/{studentId}/recent', [ExamResultController::class, 'getStudentRecentResults']);

    // Exam-specific routes
    Route::get('/exam/{examId}/all', [ExamResultController::class, 'getResultsByExamId']);
    Route::get('/exam/{examId}/statistics', [ExamResultController::class, 'getExamStatistics']);
    Route::get('/exam/{examId}/top-performers', [ExamResultController::class, 'getTopPerformers']);
});
Route::prefix('student-answers')->group(function () {
    Route::get('/', [StudentAnswerController::class, 'index']);
    Route::post('/', [StudentAnswerController::class, 'store']);
    Route::get('/{answer}', [StudentAnswerController::class, 'show']);
    Route::put('/{answer}', [StudentAnswerController::class, 'update']);
    Route::delete('/{answer}', [StudentAnswerController::class, 'destroy']);
    
});
Route::get('students/{student}/lessons/{lesson}/answers', [StudentAnswerController::class, 'getAnswersForLesson']);

// Parent-Teacher Conversations Routes
Route::prefix('parent-teacher-conversations')->group(function () {
    // Basic CRUD routes
    Route::get('/', [ParentTeacherConversationController::class, 'index']);
    Route::post('/', [ParentTeacherConversationController::class, 'store']);
    Route::get('/{conversation}', [ParentTeacherConversationController::class, 'show']);
    Route::delete('/{conversation}', [ParentTeacherConversationController::class, 'destroy']);

    // Parent specific routes
    Route::get('/parent/{parentId}/all', [ParentTeacherConversationController::class, 'getParentConversations']);

    // Teacher specific routes
    Route::get('/teacher/{teacherId}/all', [ParentTeacherConversationController::class, 'getTeacherConversations']);

    // Additional functionality routes
    Route::post('/{conversation}/mark-read', [ParentTeacherConversationController::class, 'markAsRead']);
    Route::get('/unread/count', [ParentTeacherConversationController::class, 'getUnreadCount']);
    Route::get('/recent/{userId}/{userType}', [ParentTeacherConversationController::class, 'getRecentConversations']);
    Route::post('/{conversation}/archive', [ParentTeacherConversationController::class, 'archiveConversation']);
    Route::post('/{conversation}/restore', [ParentTeacherConversationController::class, 'restoreConversation']);
});


Route::prefix('exam-answers')->group(function () {
    Route::get('/', [StudentExamAnswerController::class, 'index']); // All answers
    Route::post('/', [StudentExamAnswerController::class, 'store']); // Submit multiple answers
    Route::get('/exam/{examId}/student/{studentId}', [StudentExamAnswerController::class, 'getAnswersForExam']); // For teacher review
    Route::put('/{id}/score', [StudentExamAnswerController::class, 'updateScore']); // Update score for essay
});

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60);
});
