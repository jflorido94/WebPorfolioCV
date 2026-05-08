<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PdfService;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\Response;

class CvController extends Controller
{
    public function show(): View
    {
        return view('public.cv', ['user' => $this->loadCvUser()]);
    }

    public function print(): View
    {
        return view('public.cv-print', ['user' => $this->loadCvUser()]);
    }

    public function downloadPdf(PdfService $pdfService): Response
    {
        return $pdfService->generateCvPdf($this->loadCvUser());
    }

    private function loadCvUser(): User
    {
        return User::query()->withCv()->firstOrFail();
    }
}
