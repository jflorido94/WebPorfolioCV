<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'page',
        'ip',
        'country',
        'state',
        'city',
        'zipcode',
        'latitude',
        'longitude',
        'isp',
        'is_mobile',
        'is_vpn',
        'is_tor',
        'is_proxy',
        'is_datacenter',
        'user_agent',
        'geo_resolved',
    ];

    protected function casts(): array
    {
        return [
            'latitude'     => 'decimal:7',
            'longitude'    => 'decimal:7',
            'is_mobile'    => 'boolean',
            'is_vpn'       => 'boolean',
            'is_tor'       => 'boolean',
            'is_proxy'     => 'boolean',
            'is_datacenter'=> 'boolean',
            'geo_resolved' => 'boolean',
        ];
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }
}
