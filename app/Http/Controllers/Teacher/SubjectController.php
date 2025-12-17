<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(){
        return Subject::orderBy('level_group')->orderBy('name')->get();
    }
    public function getByLevelGroup($level_group){
        return Subject::where('level_group',$level_group)->orderBy('name')->get();
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'level_group' => 'required',
        ]);
        $subject=Subject::create([
            'name'=> $request->name,
        'level_group'=>$request->level_group,
        ]);
        return response()->json($subject, 201);
    }
    public function updateSubject(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $subject = Subject::findOrFail($id);
        $request->validate([
            'name' => 'required'.$subject->id,
            'level_group' => 'required',
        ]);
        $subject->update($request->only('name','level_group'));
        return response()->json($subject, 200);
    }
}
