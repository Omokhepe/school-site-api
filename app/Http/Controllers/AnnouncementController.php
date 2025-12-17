<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Announcement;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{

    public function store(Request $request){
            $validated = $request->validate([
                "subject"=> "required|string|max:255",
                "message"=> "required|string",
                "start_date"=> "required|date",
                "end_date"=> "required|date|after_or_equal:start_date",
            ]);
        $announcement = Announcement::create([...$validated, 'created_by'=>$request->user()->id]);

        return response()->json([
            'status'=>'success',
            'message'=> 'Announcement Created',
            'data'=>$announcement
        ], 201);
    }

    public function activeAnnouncement(): JsonResponse{
        // $today = now()->toDateString();

        // $announcement = Announcement::whereDate('start_date','<=',$today)
        // ->where('end_date','>=', value: $today)
        // ->orderBy('start_date','desc')
        // ->get();

        $today = now()->startOfDay();
    $fiveDaysFromNow = now()->addDays(5)->startOfDay();

    $announcements = Announcement::orderBy('start_date', 'asc')->get();

    $announcements = $announcements->map(function ($item) use ($today, $fiveDaysFromNow) {
        $start = Carbon::parse($item->start_date)->startOfDay();
        $end = Carbon::parse($item->end_date)->startOfDay();

        // 1. UPCOMING: Today < start_date
        if ($today->lt($start)) {
            $item->status = "upcoming";
            return $item;
        }

        // 2. ACTIVE: start <= today <= end
        if ($today->between($start, $end)) {

            // 2B. ALMOST UP: 5 days to end date
            if ($end->lte($fiveDaysFromNow)) {
                $item->status = "almost_up";
            } else {
                $item->status = "active";
            }

            return $item;
        }

        // 3. EXPIRED (optional)
        if ($today->gt($end)) {
            $item->status = "expired";
            return $item;
        }

        return $item;
    });

        return response()->json([
            'status'=> 'success',
            'data'=> $announcements,
        ]);
    }

    public function update(Request $request, $id){
        $validated = $request->validate([
            "subject"=> "required|string|max:255",
            "message"=> "required|string",
            "start_date"=> "required|date",
            "end_date"=> "required|date|after_or_equal:start_date",
        ],[
            'subject.required' => 'Subject is required.',
        'message.required' => 'Message text is required.',
        'start_date.required' => 'Start date is required.',
        'end_date.after_or_equal' => 'End date must be the same or after start date.',
        ]);

        $announcement = Announcement::findOrFail($id);
        $announcement->update($validated);
        return response()->json([
            'status'=>'success',
            'message'=> 'Announcement Updated',
            'data'=>$announcement
        ]);
    }
    public function destroy($id){
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();
        return response()->json([
            'status'=> 'success',
            'message'=> 'Announcement Deleted'
        ]);
    }
}