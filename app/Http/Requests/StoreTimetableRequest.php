<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTimetableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'class_id'   => ['required','integer','exists:school_classes,id'],
            'subject_id' => ['required','integer','exists:subjects,id'],
            'teacher_id' => ['nullable','integer','exists:users,id'],
            'day'        => ['required', Rule::in(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'])],
            'start_time' => ['required','date_format:H:i'],
            'end_time'   => ['required','date_format:H:i','after:start_time'],
            'id'         => ['sometimes','integer'] // for updates
        ];
    }
}