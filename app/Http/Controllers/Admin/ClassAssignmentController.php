<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ClassAssignmentController extends Controller
{
    public function assignSingle(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
        ]);
        $student = User::where('id', $id)->where('role', 'student')->first();
        if(!$student){
            return response()->json(['error' => 'Student not found'], 404);
        }
        $student->update(['class_id' => $request->class_id]);
        return response()->json(['message' => 'Class successfully updated for student'], 200);
    }
    public function assignBulk( Request $request){
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:users,id',
        ]);

        User::whereIn('id', $request->student_ids)->where('role', 'student')->update(['class_id' => $request->class_id]);
        return response()->json(['message' => 'Class successfully updated for selected student'], 200);
    }
}
