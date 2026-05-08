<?php

namespace App\Jobs;

use App\Models\PageMetric;
use App\Services\GeoLookupService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EnrichPageMetric implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(
        private readonly int $pageMetricId,
    ) {}

    public function handle(GeoLookupService $geoLookupService): void
    {
        $metric = PageMetric::find($this->pageMetricId);

        if ($metric === null || $metric->geo_resolved) {
            return;
        }

        $geo = $geoLookupService->lookup($metric->ip);

        $metric->update([
            'country'       => $geo['country']       ?? null,
            'state'         => $geo['state']         ?? null,
            'city'          => $geo['city']          ?? null,
            'zipcode'       => $geo['zipcode']       ?? null,
            'latitude'      => $geo['latitude']      ?? null,
            'longitude'     => $geo['longitude']     ?? null,
            'isp'           => $geo['isp']           ?? null,
            'is_mobile'     => $geo['is_mobile']     ?? null,
            'is_vpn'        => $geo['is_vpn']        ?? null,
            'is_tor'        => $geo['is_tor']        ?? null,
            'is_proxy'      => $geo['is_proxy']      ?? null,
            'is_datacenter' => $geo['is_datacenter'] ?? null,
            'geo_resolved'  => true,
        ]);
    }
}
