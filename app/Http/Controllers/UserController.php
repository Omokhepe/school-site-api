<?php

namespace App\Http\Controllers;

use App\Models\StaffProfile;
use App\Models\StudentProfile;
use App\Models\User;
use App\Service\UserIdGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    protected $idGenerator;

    public function __construct(UserIdGeneratorService $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(User::all(), 200);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'username' => 'required|unique:users,username',
                'role' => 'nullable|in:admin,teacher,student',
                'class_id' => 'nullable|exists:school_classes,id',
                'date_of_birth' => 'required|date',
                'state_of_origin' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone_number' => 'nullable|string|max:20',
                'address' => 'required|string|max:500',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'gender' => 'required|in:male,female',
                // STUDENT ONLY
                'parent_first_name' => 'required_if:role,student',
                'parent_last_name' => 'required_if:role,student',
                'parent_email' => 'nullable|email',
                'parent_phone' => 'required_if:role,student',
                'parent_address' => 'required_if:role,student',
                // TEACHER/ADMIN ONLY
                'highest_education' => 'required_if:role,teacher,admin',
                // 'degree' => 'required_if:role,teacher,admin',
                'course' => 'required_if:role,teacher,admin',
                'reference_name' => 'required_if:role,teacher,admin',
                'reference_phone' => 'required_if:role,teacher,admin',
                'reference_email' => 'nullable|email',
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('profiles', 'public');
            }

            Log::debug($request);
            if ($request->role === 'student') {
                $generatedId = $this->idGenerator->generate('student');
                $defaultPassword = substr($generatedId, -6);
                $mustChangePassword = false;
            }
            if ($request->role === 'teacher' || $request->role === 'admin') {
                $generatedId = $this->idGenerator->generate('teacher');
                $defaultPassword = 'Default@123';
                $mustChangePassword = true;
            }

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $request->username,
                'user_id' => $generatedId,
                'gender' => $request->gender,
                'password' => Hash::make($defaultPassword),
                'role' => $request->role ?? 'student',
                'class_id' => $request->class_id ?? null,
                'must_change_password' => $mustChangePassword,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'image' => $imagePath,
                'date_of_birth' => $request->date_of_birth,
                'state_of_origin' => $request->state_of_origin,
            ]);

            if ($user->role === 'student') {
                $student = StudentProfile::create([
                    'user_id' => $generatedId,
                    'parent_first_name' => $request->parent_first_name,
                    'parent_last_name' => $request->parent_last_name,
                    'parent_email' => $request->parent_email,
                    'parent_phone' => $request->parent_phone,
                    'parent_address' => $request->parent_address,
                ]);
            }

            if ($user->role === 'teacher' || $user->role === 'admin') {
                $student = StaffProfile::create([
                    'user_id' => $generatedId,
                    'highest_education' => $request->highest_education,
                    // 'degree' => $request->degree,
                    'course' => $request->course,
                    'reference_name' => $request->reference_name,
                    'reference_phone' => $request->reference_phone,
                    'reference_email' => $request->reference_email,
                ]);
            }
            return response()->json(['status' => 'success', 'user' => $user->load('class'), 'message' => 'User Created', 'Default Password' => $defaultPassword], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'error' => $e->errors(), 'message' => 'Validation Failed'], 422);
        } catch (\Exception $e) {
            Log::error('User creation error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'error_detail' => $e->getMessage(), 'message' => 'An unexpected error occurred while creating the user'], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User Deleted'], 200);
    }

    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // VALIDATION RULES
            $rules = [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'username' => 'required|unique:users,username,' . $user->id,
                'date_of_birth' => 'required|date',
                'state_of_origin' => 'required|string',
                'phone_number' => 'nullable',
                'address' => 'required|string',
                'email' => 'nullable|email',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ];

            // STUDENT VALIDATION
            if ($user->role === 'student') {
                $rules = array_merge($rules, [
                    'parent_first_name' => 'required',
                    'parent_last_name' => 'required',
                    'parent_email' => 'nullable|email',
                    'parent_phone' => 'required',
                    'parent_address' => 'required',
                ]);
            }

            // TEACHER / ADMIN VALIDATION
            if ($user->role === 'teacher' || $user->role === 'admin') {
                $rules = array_merge($rules, [
                    'highest_education' => 'required',
                    'degree' => 'required',
                    'course' => 'required',
                    'reference_name' => 'required',
                    'reference_phone' => 'required',
                    'reference_email' => 'nullable|email',
                ]);
            }

            $request->validate($rules);

            // UPDATE IMAGE (OPTIONAL)
            $imagePath = $user->image;
            if ($request->hasFile('image')) {
                if ($user->image && Storage::disk('public')->exists($user->image)) {
                    Storage::disk('public')->delete($user->image);
                }
                $imagePath = $request->file('image')->store('profiles', 'public');
            }

            // UPDATE COMMON FIELDS
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $request->username,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'state_of_origin' => $request->state_of_origin,
                'image' => $imagePath,
            ]);

            // UPDATE STUDENT PROFILE
            if ($user->role === 'student') {
                $student = StudentProfile::firstOrCreate(['user_id' => $user->id]);
                $student->update([
                    'parent_first_name' => $request->parent_first_name,
                    'parent_last_name' => $request->parent_last_name,
                    'parent_email' => $request->parent_email,
                    'parent_phone' => $request->parent_phone,
                    'parent_address' => $request->parent_address,
                ]);
            }

            // UPDATE STAFF PROFILE
            if ($user->role === 'teacher' || $user->role === 'admin') {
                $staff = StaffProfile::firstOrCreate(['user_id' => $user->id]);
                $staff->update([
                    'highest_education' => $request->highest_education,
                    'degree' => $request->degree,
                    'course' => $request->course,
                    'reference_name' => $request->reference_name,
                    'reference_phone' => $request->reference_phone,
                    'reference_email' => $request->reference_email,
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'User Role Updated', 'user' => $user], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Unexpected error occurred',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

    public function teachers()
    {
        return response()->json(
            // User::whereIn('role', ['teacher', 'admin'])
            User::where('role', 'teacher')
                ->with('staffProfile')
                ->get(),
            200
        );
    }

    public function studentsByClass($classId)
    {
        return response()->json(User::where('role', 'student')->with('studentProfile')->where('class_id', $classId)->get(), 200);
    }

    public function students()
    {
        return User::where('role', 'student')->with('studentProfile')->with('class')->get();
    }
}