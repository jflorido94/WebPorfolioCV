<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAccountEmailRequest;
use App\Http\Requests\Admin\UpdateAccountPasswordRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', ['user' => Auth::user()]);
    }

    public function updateEmail(UpdateAccountEmailRequest $request): RedirectResponse
    {
        Auth::user()->update(['email' => $request->validated('email')]);

        return redirect()->route('admin.settings.index')->with('success', 'Email actualizado exitosamente');
    }

    public function updatePassword(UpdateAccountPasswordRequest $request): RedirectResponse
    {
        Auth::user()->update(['password' => Hash::make($request->validated('password'))]);

        return redirect()->route('admin.settings.index')->with('success', 'Contraseña actualizada exitosamente');
    }
}
