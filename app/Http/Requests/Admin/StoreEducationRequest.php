<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'institution' => 'required|string|max:150',
            'location' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:2099',
            'show_in_web' => 'nullable|boolean',
            'show_in_pdf' => 'nullable|boolean',
        ];
    }
}
