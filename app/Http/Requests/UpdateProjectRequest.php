<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'client_address' => 'nullable|string|max:1000',
            'pic_name' => 'nullable|string|max:255',
            'pic_no' => 'nullable|string|max:255',
            'period' => 'nullable|string|max:255',
            'status' => 'required|in:draft,planned,completed',
        ];
    }
}
