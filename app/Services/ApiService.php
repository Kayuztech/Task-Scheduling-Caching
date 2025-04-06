<?php

namespace App\Services;

use App\Models\Apilog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiService
{
    public function getNewsData()
    {
        return Cache::remember('top_news', 5, function () {
            try {
                $response = Http::get('https://gnews.io/api/v4/top-headlines', [
                    'token' => 'c06a0e8f850ba697014e626a7f18e446',
                    'lang' => 'en',
                    'country' => 'ng' // or 'us', 'gb', etc.
                ]);

                if ($response->failed()) {
                    Log::error('Failed to fetch news data from the API.');
                    return Cache::get('top_news');
                }

                Apilog::create([
                    'endpoint' => 'https://gnews.io/api/v4/top-headlines',
                    'method' => 'GET',
                    'response' => $response->body()
                ]);

                return $response->json();

            } catch (\Exception $e) {
                Log::error('Error fetching news data: ' . $e->getMessage());
                return Cache::get('top_news');
            }
        });
    }
}
