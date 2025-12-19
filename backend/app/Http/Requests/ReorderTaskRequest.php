<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class ReorderTaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'task_ids' => ['required', 'array'],
            'task_ids.*' => ['required', 'integer', 'exists:tasks,id'],
        ];
    }
}
