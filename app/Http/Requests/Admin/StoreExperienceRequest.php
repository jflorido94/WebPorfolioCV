<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'role' => 'required|string|max:150',
            'company' => 'required|string|max:150',
            'location' => 'nullable|string|max:100',
            'period' => 'required|string|max:60',
            'description' => 'nullable|string',
            'competencies' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'show_in_web' => 'nullable|boolean',
            'show_in_pdf' => 'nullable|boolean',
        ];
    }
}
