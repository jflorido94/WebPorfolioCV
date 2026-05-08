<?php

namespace App\Http\Middleware;

use App\Jobs\EnrichPageMetric;
use App\Models\PageMetric;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordPageMetric
{
    private const BOT_SIGNATURES = [
        'bot', 'crawler', 'spider', 'slurp', 'mediapartners',
        'bingpreview', 'facebookexternalhit', 'whatsapp', 'twitterbot',
        'linkedinbot', 'googlebot', 'baiduspider', 'yandexbot',
        'duckduckbot', 'sogou', 'exabot', 'ia_archiver', 'semrushbot',
        'ahrefsbot', 'mj12bot', 'dotbot', 'rogerbot',
    ];

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if ($response->getStatusCode() >= 400) {
            return;
        }

        if ($request->user() !== null) {
            return;
        }

        $userAgent = strtolower((string) $request->userAgent());
        foreach (self::BOT_SIGNATURES as $signature) {
            if (str_contains($userAgent, $signature)) {
                return;
            }
        }

        $page = match (true) {
            $request->routeIs('home')    => 'home',
            $request->routeIs('cv.show') => 'cv',
            default                      => null,
        };

        if ($page === null) {
            return;
        }

        // Cloudflare Tunnel sends the real visitor IP in CF-Connecting-IP.
        $ip = $request->header('CF-Connecting-IP') ?? $request->ip();

        $alreadyRecorded = PageMetric::query()
            ->where('ip', $ip)
            ->where('page', $page)
            ->whereDate('created_at', today())
            ->exists();

        if ($alreadyRecorded) {
            return;
        }

        $metric = PageMetric::create([
            'page'         => $page,
            'ip'           => $ip,
            'user_agent'   => mb_substr((string) $request->userAgent(), 0, 500),
            'geo_resolved' => false,
        ]);

        EnrichPageMetric::dispatch($metric->id);
    }
}
