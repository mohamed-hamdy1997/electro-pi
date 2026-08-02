<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $titleRules = $this->isMethod('post') ? ['required'] : ['sometimes', 'required'];

        return [
            'title'       => [...$titleRules, 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority'    => ['nullable', Rule::enum(TaskPriorityEnum::class)],
            'status'      => ['nullable', Rule::enum(TaskStatusEnum::class)],
            'due_date'    => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}
