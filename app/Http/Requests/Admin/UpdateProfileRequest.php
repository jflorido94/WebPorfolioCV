<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'location' => 'nullable|string|max:100',
            'github_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'avatar_initials' => 'nullable|string|max:4',
            'avatar' => 'nullable|file|image|max:2048',
            'remove_avatar' => 'nullable|boolean',
        ];
    }
}
