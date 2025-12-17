<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Note;
use App\Models\NoteContent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class TeacherNoteController extends Controller
{
    public function saveContents(Note $note, $contents){
        if(!$contents) return;

        foreach($contents as $index=>$item){
            $imagePath=null;

            if(isset($item['image'])){
                $imagePath=$item['image']->store('note/image', 'public');
                // $imagePath->images()->create(['image_path' => $imagePath]);
            }

            NoteContent::create([
                'note_id'=> $note->id,
                // 'order'=>$index+1,
                'subtitle'=>$item['subtitle']??null,
                'content'=>$item['content']??null,
                // 'type'=>$item['type']??'text',
                'image_path'=> $imagePath,

            ]);
        }
    }
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        if (is_string($request->weeks)) {
            $request->merge([
                'weeks' => json_decode($request->weeks, true)
            ]);
        }
        Log::info('INCOMING RAW', $request->all());
        $validated=$request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'weeks' => 'required|array|min:1',
            'weeks.*' => 'integer|min:1|max:13',
            'school_year'=> 'required|string',
            'term' => 'required|in:1,2,3',
            'topic' => 'required|string',
            'subtopics' => 'array',
            'subtopics.*.images' => 'required_if:subtopics.*.type,image|image|mimes:jpg,jpeg,png,webp|max:4096',

        ],[
            'class_id.required'=>'Class is required.',
            'class_id.exists'=>'Selected Class does not exist.',

            'subject_id.required'=>'Subject is required.',
            'subject_id.exists'=>'Selected Subject does not exist.',

            'weeks.required'=>'At least one week must be selected.',
            // 'weeks.array'=>'Weeks must be an array.',
            'weeks.min'=>'At least one week must be selected.',
            'weeks.*.integer'=>'Each week must be a valid number.',
            'weeks.*.min'=>'Week cannot be less than 1.',
            'weeks.*.max'=>'Week cannot be greater than 13.',

            'term.required'=>'Term is required.',
            'term.in'=>'Term must be a valid term.',

            'topic.required'=>'Topic is required.',
            'subtopics.image'=>'Uploaded File Must be Image File Type'
            // 'note_content.required'=>'Note Content is required.',

        ]);
        $note=Note::create([
            'note_id' => 'LN-' . now()->format('Y') . '-' . rand(100000, 999999),
            'teacher_id'=>$request->user()->id,
            'class_id'=>$validated['class_id'],
            'subject_id'=>$validated['subject_id'],
            'term'=>$validated['term'],
            'weeks'=>$validated['weeks'],
            'school_year'=>$request->school_year,
            'topic'=>$validated['topic'],
        ]);

        $this->saveContents($note, $request->subtopics);

        return response()->json(['status'=>'success', 'message'=>'Lesson note created successfully', 'data'=> $note->load('subtopics')], 201);
    }

    public function updateNotes(Request $request, $id){
        // $note=Note::where('id',$id)->where('teacher_id',$request->user()->id)->first();
        $note=Note::findOrFail($id);


        $this->authorizeNote( $request,$note);

        $note->update($request->only(['class_id','subject_id', 'term', 'weeks', 'topic']));

        if($request->has('subtitle')){
            $note->subtopics()->delete();
            $this->saveContents($note, $request->contents);
        }

        // return response()->json($note);
        return response()->json(['status'=>'success', 'message'=>'Lesson note Updated successfully', 'data'=> $note->load('subtopics')], 201);

    }
    public function index(Request $request){
        // return Note::where('teacher_id',$request->user()->id)->with(['class','subject'])->orderBy('term')->orderBy('week')->get();
        $notes = Note::with('subtopics')
            ->when($request->user()->role === 'teacher', function ($q) use ($request) {
                $q->where('teacher_id', $request->user()->id);
            })
            ->latest()
            ->get();

        return response()->json($notes->load('class'));
    }

    public function show(Request $request, $id)
    {
        $note = Note::with('subtopics')->findOrFail($id);

        $this->authorizeNote($request, $note);

        return response()->json($note->load('class'));
    }

    public function destroy(Request $request, $id)
    {
        $note = Note::findOrFail($id);
        $this->authorizeNote($request,$note);

        $note->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function authorizeNote(Request $request, Note $note){
        $user = $request->user();

        if($user->role === 'admin') return true;

        if($note->teacher_id !== $user->id) {
            abort(403, 'Unauthorized');
        };
    }
}