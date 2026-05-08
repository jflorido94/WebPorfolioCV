<?php

namespace App\Services;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class PdfService
{
    public function generateCvPdf(User $user): Response
    {
        $html = view('public.cv-print', ['user' => $user])->render();
        $filename = 'cv_' . Str::slug($user->name ?: 'usuario') . '.pdf';

        return Pdf::loadHTML($html)
            ->setPaper('a4')
            ->setOption('isPhpEnabled', true)
            ->download($filename);
    }
}
