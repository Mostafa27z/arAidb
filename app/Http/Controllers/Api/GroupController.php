<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::paginate(10);
        return response()->json([
            'status' => 200,
            'data' => GroupResource::collection($groups),
            'pagination' => [
                'current_page' => $groups->currentPage(),
                'last_page' => $groups->lastPage(),
                'total' => $groups->total(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:teachers,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);

        $group = Group::create($request->all());

        return response()->json([
            'status' => 201,
            'data' => new GroupResource($group)
        ], 201);
    }
public function availableForStudent($studentId)
{
    // هات كل الجروبات مع علاقة العضوية (لو الطالب له عضوية فيها)
    $groups = \App\Models\Group::with(['teacher', 'members' => function ($q) use ($studentId) {
        $q->where('student_id', $studentId);
    }])->get();

    // عدل البيانات عشان ترجع مع حالة الانضمام
    $data = $groups->map(function ($group) use ($studentId) {
        $member = $group->members->first(); // لو ليه عضوية
        return [
            'id' => $group->id,
            'title' => $group->title,
            'description' => $group->description,
            'teacher_name' => $group->teacher->user->name ?? 'غير معروف',
            'membership_status' => $member->status ?? null // null معناها لسه ما قدمش
        ];
    });

    return response()->json([
        'status' => 200,
        'data' => $data
    ]);
}

public function getGroupsForStudent($studentId)
{
    $groups = Group::whereHas('members', function ($q) use ($studentId) {
        $q->where('student_id', $studentId);
    })->with(['sessions'])->get();

    return response()->json([
        'status' => 200,
        'data' => GroupResource::collection($groups),
    ]);
}

    public function show(Group $group)
    {
        return response()->json([
            'status' => 200,
            'data' => new GroupResource($group)
        ]);
    }

    public function update(Request $request, Group $group)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'teacher_id' => 'sometimes|exists:teachers,id',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time'
        ]);

        $group->update($validatedData);

        return response()->json([
            'status' => 200,
            'data' => new GroupResource($group)
        ]);
    }

    public function destroy(Group $group)
    {
        $group->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Group deleted successfully'
        ]);
    }
}
