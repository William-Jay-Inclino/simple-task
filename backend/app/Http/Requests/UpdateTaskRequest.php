<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'statement' => ['sometimes', 'string', 'max:100'],
            'task_date' => ['sometimes', 'date'],
            'is_completed' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'statement.max' => 'The task statement cannot exceed 100 characters.',
        ];
    }
}
