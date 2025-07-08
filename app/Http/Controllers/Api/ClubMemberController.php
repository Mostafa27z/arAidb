<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClubMemberResource;
use App\Models\ClubMember;
use Illuminate\Http\Request;
use App\Models\Club;
class ClubMemberController extends Controller
{
    /**
     * Get all club members.
     */
    public function index(Request $request) 
{ 
    $query = ClubMember::with(['student.user']); // هنا هنجيب بيانات الطالب مع اليوزر

    if ($request->has('student_id')) { 
        $query->where('student_id', $request->student_id); 
    } 
    
    if ($request->has('club_id')) { 
        $query->where('club_id', $request->club_id); 
    } 

    $members = $query->get(); 

    return response()->json(['data' => $members]); 
}


    /**
     * Store a new club member.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'club_id' => 'required|exists:clubs,id',
        ]);

        $clubMember = ClubMember::create([
            'student_id' => $request->student_id,
            'club_id' => $request->club_id,
            'status' => 'pending'
        ]);


        return response()->json([
            'status' => 201,
            'data' => new ClubMemberResource($clubMember),
        ], 201);
    }

    /**
     * Show a single club member.
     */
    public function show(ClubMember $clubMember)
    {
        return response()->json([
            'status' => 200,
            'data' => new ClubMemberResource($clubMember),
        ], 200);
    }

    /**
     * Delete a club member.
     */
   public function destroy($id)
{
    $member = \App\Models\ClubMember::find($id);

    if (!$member) {
        return response()->json([
            'status' => 404,
            'message' => 'العضو غير موجود'
        ], 404);
    }

    $member->delete();

    return response()->json([
        'status' => 200,
        'message' => 'تم حذف العضو بنجاح'
    ]);
}

public function approve(Request $request, $id)
{
    $member = ClubMember::findOrFail($id);

    // تحقق أن المدرس هو صاحب الجروب
    if ($member->club->teacher_id !== $request->teacher_id) {
        return response()->json(['message' => 'غير مصرح'], 403);
    }

    $member->status = 'approved';
    $member->save();

    return response()->json(['message' => 'تم قبول الطالب']);
}
// public function approve($id)
// {
//     $member = \App\Models\ClubMember::findOrFail($id);
//     $member->status = 'approved';
//     $member->save();

//     return response()->json([
//         'status' => 200,
//         'message' => 'Membership approved successfully',
//         'data' => $member,
//     ]);
// }

public function reject($id)
{
    $member = \App\Models\ClubMember::findOrFail($id);
    $member->status = 'rejected';
    $member->save();

    return response()->json([
        'status' => 200,
        'message' => 'Membership rejected successfully',
        'data' => $member,
    ]);
}

}
