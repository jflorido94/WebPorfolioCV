<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:60',
            'category' => 'required|string|max:40',
            'show_in_web' => 'nullable|boolean',
            'show_in_pdf' => 'nullable|boolean',
        ];
    }
}
