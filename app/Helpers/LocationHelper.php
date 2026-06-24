<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LocationHelper
{
    /**
     * Reverse-geocode (lat, lng) → human-readable address.
     * Results are cached for 24 h to avoid hammering Nominatim.
     */
    public static function getAddress(float $lat, float $lng): string
    {
        // Round to 4 decimal places (~11 m precision) for better cache hits
        $lat = round($lat, 4);
        $lng = round($lng, 4);

        $cacheKey = "geo_address_{$lat}_{$lng}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($lat, $lng) {
            try {
                $response = Http::withHeaders([
                    // Nominatim requires a User-Agent identifying your app
                    'User-Agent' => config('app.name', 'AttendanceApp') . '/1.0',
                ])
                ->timeout(5)
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat'            => $lat,
                    'lon'            => $lng,
                    'format'         => 'json',
                    'zoom'           => 18,
                    'addressdetails' => 1,
                ]);

                if ($response->successful()) {
                    $data    = $response->json();
                    $address = $data['address'] ?? [];

                    $parts = array_filter([
                        $address['road']         ?? null,
                        $address['suburb']       ?? ($address['neighbourhood'] ?? null),
                        $address['city']         ?? ($address['town'] ?? ($address['village'] ?? null)),
                        $address['state']        ?? null,
                    ]);

                    return !empty($parts)
                        ? implode(', ', $parts)
                        : ($data['display_name'] ?? "Lat: {$lat}, Lng: {$lng}");
                }
            } catch (\Throwable $e) {
                Log::warning('Reverse geocoding failed', [
                    'lat'   => $lat,
                    'lng'   => $lng,
                    'error' => $e->getMessage(),
                ]);
            }

            return "Lat: {$lat}, Lng: {$lng}";
        });
    }

    /**
     * Google Maps URL for a coordinate pair.
     */
    public static function getGoogleMapsUrl(float $lat, float $lng): string
    {
        return "https://www.google.com/maps?q={$lat},{$lng}";
    }

    /**
     * Haversine distance between two coordinates (kilometres).
     */
    public static function getDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r    = 6371; // Earth radius in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return round($r * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
    }
}