<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreTimetableRequest;

class TimetableController extends Controller
{
    // GET /api/timetable?class_id=#
    public function index(Request $req)
    {
        $classId = $req->query('class_id');
        $query = Timetable::with(['subject','teacher'])
                  ->when($classId, fn($q)=>$q->where('class_id', $classId))
                  ->orderBy('day')
                  ->orderBy('start_time');
        return response()->json($query->get());
    }

    // POST /api/timetable
    public function store(StoreTimetableRequest $req)
    {
        Log::info('INCOMING RAW', $req->all());
        $data = $req->validated();
        $conflict = $this->checkConflict(null, $data);
        if ($conflict) return response()->json(['error' => $conflict], 422);

        $entry = Timetable::create($data);
        return response()->json($entry->load('subject','teacher'));
    }

    // PUT /api/timetable/{id}
    public function update(StoreTimetableRequest $req, $id)
    {
        $data = $req->validated();
        $conflict = $this->checkConflict($id, $data);
        if ($conflict) return response()->json(['error' => $conflict], 422);

        $entry = Timetable::findOrFail($id);
        $entry->update($data);
        return response()->json($entry->load('subject','teacher'));
    }
    // DELETE /api/timetable/{id}
    public function destroy($id)
    {
        $entry = Timetable::findOrFail($id);
        $entry->delete();
        return response()->json(['success'=>true]);
    }

    // Conflict check: returns message or null
    protected function checkConflict($excludeId, array $data)
    {
        // same class/time clash OR same teacher/time clash
        // overlap if: start < existing.end AND end > existing.start
        $classId = $data['class_id'];
        $teacherId = $data['teacher_id'] ?? null;
        $day = $data['day'];
        $start = $data['start_time'];
        $end = $data['end_time'];

        $baseQuery = Timetable::where('day', $day)
            ->where(function($q) use ($start, $end) {
                $q->where(function($s) use ($start, $end) {
                    $s->where('start_time','<', $end)
                      ->where('end_time','>', $start);
                });
            });

        if ($excludeId) $baseQuery->where('id','<>',$excludeId);

        // class conflict
        $classConflict = (clone $baseQuery)->where('class_id',$classId)->first();
        if ($classConflict) {
            return 'Class already has a subject in this timeslot.';
        }

        // teacher conflict
        if ($teacherId) {
            $teacherConflict = (clone $baseQuery)->where('teacher_id',$teacherId)->first();
            if ($teacherConflict) {
                return 'Assigned teacher has another class at this time.';
            }
        }

        return null;
    }

    public function teacherTimetable(Request $request)
    {
        return Timetable::where('teacher_id', $request->user()->id)
        ->with(['subject','class'])
        ->orderBy('day')
        ->orderBy('start_time')
        ->get();
    }


}