<?php

namespace Database\Factories;

use App\Models\PageMetric;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PageMetric>
 */
class PageMetricFactory extends Factory
{
    public function definition(): array
    {
        return [
            'page'          => fake()->randomElement(['home', 'cv']),
            'ip'            => fake()->ipv4(),
            'country'       => fake()->country(),
            'state'         => fake()->state(),
            'city'          => fake()->city(),
            'zipcode'       => fake()->postcode(),
            'latitude'      => fake()->latitude(),
            'longitude'     => fake()->longitude(),
            'isp'           => fake()->company() . ' ISP',
            'is_mobile'     => fake()->boolean(20),
            'is_vpn'        => fake()->boolean(10),
            'is_tor'        => fake()->boolean(2),
            'is_proxy'      => fake()->boolean(5),
            'is_datacenter' => fake()->boolean(15),
            'user_agent'    => fake()->userAgent(),
            'geo_resolved'  => true,
        ];
    }

    public function unresolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'country'       => null,
            'state'         => null,
            'city'          => null,
            'zipcode'       => null,
            'latitude'      => null,
            'longitude'     => null,
            'isp'           => null,
            'is_mobile'     => null,
            'is_vpn'        => null,
            'is_tor'        => null,
            'is_proxy'      => null,
            'is_datacenter' => null,
            'geo_resolved'  => false,
        ]);
    }
}
