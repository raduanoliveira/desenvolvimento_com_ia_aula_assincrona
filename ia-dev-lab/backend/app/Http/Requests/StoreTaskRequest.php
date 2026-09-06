<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'due_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'priority' => ['sometimes', 'nullable', Rule::in(['high', 'medium', 'low'])],
        ];
    }
}
