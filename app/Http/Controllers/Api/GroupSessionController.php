<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupSessionController extends Controller
{
    public function store(Request $request)
{
    $data = $request->validate([
        'group_id' => 'required|exists:groups,id',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'session_time' => 'required|date',
        'link' => 'required|url'
    ]);

    $session = \App\Models\GroupSession::create($data);

    return response()->json(['status' => 201, 'data' => $session]);
}

public function getByGroup($groupId)
{
    $sessions = \App\Models\GroupSession::where('group_id', $groupId)->orderBy('session_time')->get();
    return response()->json(['status' => 200, 'data' => $sessions]);
}
public function destroy($id)
{
    $session = \App\Models\GroupSession::findOrFail($id);
    $session->delete();

    return response()->json([
        'status' => 200,
        'message' => 'تم حذف الجلسة بنجاح'
    ]);
}

}
