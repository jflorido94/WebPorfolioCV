<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeoLookupService
{
    private const API_BASE = 'https://api.ipquery.io/';
    private const TIMEOUT_SECONDS = 5;

    /**
     * @return array{
     *   country: string|null, state: string|null, city: string|null,
     *   zipcode: string|null, latitude: float|null, longitude: float|null,
     *   isp: string|null, is_mobile: bool|null, is_vpn: bool|null,
     *   is_tor: bool|null, is_proxy: bool|null, is_datacenter: bool|null
     * }|null
     */
    public function lookup(string $ip): ?array
    {
        if ($this->isNonPublicIp($ip)) {
            return null;
        }

        try {
            $response = Http::timeout(self::TIMEOUT_SECONDS)
                ->get(self::API_BASE . rawurlencode($ip));

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            $loc  = $data['location'] ?? [];
            $risk = $data['risk']     ?? [];

            return [
                'country'      => $loc['country']      ?? null,
                'state'        => $loc['state']        ?? null,
                'city'         => $loc['city']         ?? null,
                'zipcode'      => $loc['zipcode']      ?? null,
                'latitude'     => isset($loc['latitude'])  ? (float) $loc['latitude']  : null,
                'longitude'    => isset($loc['longitude']) ? (float) $loc['longitude'] : null,
                'isp'          => $data['isp']['isp']  ?? null,
                'is_mobile'    => isset($risk['is_mobile'])     ? (bool) $risk['is_mobile']     : null,
                'is_vpn'       => isset($risk['is_vpn'])        ? (bool) $risk['is_vpn']        : null,
                'is_tor'       => isset($risk['is_tor'])        ? (bool) $risk['is_tor']        : null,
                'is_proxy'     => isset($risk['is_proxy'])      ? (bool) $risk['is_proxy']      : null,
                'is_datacenter'=> isset($risk['is_datacenter']) ? (bool) $risk['is_datacenter'] : null,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    // Returns true for loopback, private ranges (192.168.x, 10.x, 172.16-31.x) and invalid IPs.
    public function isNonPublicIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}
