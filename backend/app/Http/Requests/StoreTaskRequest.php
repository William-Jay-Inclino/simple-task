<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'statement' => ['required', 'string', 'max:100'],
            'task_date' => ['required', 'date'],
            'is_completed' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'statement.required' => 'The task statement is required.',
            'statement.max' => 'The task statement cannot exceed 100 characters.',
        ];
    }
}
