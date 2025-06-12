<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UserResource;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ParentModel; // لو عندك جدول parents
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->all();

        if (!isset($data['role'])) {
            $data['role'] = 'student';
        }

        $validated = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'in:student,parent,teacher,admin,moderator',
        ])->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
        ]);

        $related_id = null;

        // Create related model if applicable
        if ($user->role === 'student') {
            $student = Student::create([
                'student_id' => $user->id,
                'parent_id' => null,
                'grade_level' => null,
            ]);
            $related_id = $student->id;
        } elseif ($user->role === 'teacher') {
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'qualification' => null,
                'bio' => null,
            ]);
            $related_id = $teacher->id;
        } elseif ($user->role === 'parent') {
            $parent = ParentModel::create([
                'user_id' => $user->id,
            ]);
            $related_id = $parent->id;
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 201,
            'message' => 'User registered successfully',
            'user' => $user,
            'related_id' => $related_id,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $related_id = null;

        if ($user->role === 'student') {
            $student = Student::where('student_id', $user->id)->first();
            $related_id = $student?->id;
        } elseif ($user->role === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            $related_id = $teacher?->id;
        } elseif ($user->role === 'parent') {
            $parent = ParentModel::where('user_id', $user->id)->first();
            $related_id = $parent?->id;
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 60 * 60 * 24 * 7,
            'user' => new UserResource($user),
            'related_id' => $related_id
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => 'Logout successful'
        ], 200);
    }
}
