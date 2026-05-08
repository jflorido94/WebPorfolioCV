<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdateAccountPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! Hash::check($value, $this->user()->password)) {
                    $fail('La contraseña actual no es correcta.');
                }
            }],
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
