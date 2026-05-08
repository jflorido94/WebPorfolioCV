<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageMetric;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class MetricsController extends Controller
{
    public function index(): View
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $metrics = PageMetric::query()
            ->latestFirst()
            ->paginate(50);

        return view('admin.metrics.index', compact('metrics'));
    }
}
