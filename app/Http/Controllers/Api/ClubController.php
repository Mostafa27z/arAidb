<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClubResource;
use App\Models\Club;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    public function clubsWithLastMessage()
    {
        $clubs = Club::withCount(['members as members_count' => function ($q) {
            $q->where('status', 'approved');
        }])
        ->with(['messages' => function ($q) {
            $q->latest()->limit(1);
        }])
        ->latest()
        ->get();

        $data = $clubs->map(function ($club) {
            $lastMessage = $club->messages->first();
            return [
                'id' => $club->id,
                'name' => $club->name,
                'description' => $club->description,
                'members_count' => $club->members_count,
                'last_message' => $lastMessage ? $lastMessage->message : null,
                'last_message_date' => $lastMessage ? $lastMessage->created_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function index()
    {
        $clubs = Club::withCount(['members as members_count' => function ($q) {
            $q->where('status', 'approved');
        }])->latest()->paginate(10);

        return response()->json([
            'status' => 200,
            'data' => ClubResource::collection($clubs),
            'pagination' => [
                'current_page' => $clubs->currentPage(),
                'last_page' => $clubs->lastPage(),
                'total' => $clubs->total(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:clubs,name|max:255',
            'description' => 'nullable|string',
        ]);

        $club = Club::create([
            'name' => $request->name,
            'description' => $request->description,
            'teacher_id' => $request->teacher_id
        ]);

        return response()->json([
            'status' => 201,
            'data' => new ClubResource($club),
        ], 201);
    }

    public function show(Club $club)
    {
        return response()->json([
            'status' => 200,
            'data' => new ClubResource($club),
        ], 200);
    }

    public function update(Request $request, Club $club)
    {
        $request->validate([
            'name' => 'sometimes|string|unique:clubs,name,' . $club->id . '|max:255',
            'description' => 'sometimes|string',
        ]);

        $club->update($request->only('name', 'description'));

        return response()->json([
            'status' => 200,
            'message' => 'Club updated successfully.',
            'data' => new ClubResource($club),
        ], 200);
    }

    public function destroy(Club $club)
    {
        $club->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Club deleted successfully.',
        ], 200);
    }

    public function getClubsForStudent($studentId)
    {
        $clubs = Club::whereHas('members', function ($query) use ($studentId) {
            $query->where('student_id', $studentId)
                ->where('status', 'approved');
        })
        ->withCount(['members as members_count' => function ($q) {
            $q->where('status', 'approved');
        }])
        ->latest()
        ->get();

        return response()->json([
            'status' => 200,
            'data' => ClubResource::collection($clubs),
        ]);
    }

    public function getClubsForTeacher($teacherId)
    {
        $clubs = Club::where('teacher_id', $teacherId)
        ->withCount(['members as members_count' => function ($q) {
            $q->where('status', 'approved');
        }])
        ->latest()
        ->get();

        return response()->json([
            'status' => 200,
            'data' => ClubResource::collection($clubs),
        ]);
    }
}
