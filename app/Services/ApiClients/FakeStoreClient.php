<?php

namespace App\Services\ApiClients;

use App\Contracts\Services\ApiClientInterface;
use App\Exceptions\ApiFetchException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FakeStoreClient implements ApiClientInterface
{
    private const BASE_URL = 'https://fakestoreapi.com';
    private const MAX_ID = 20;
    private const TIMEOUT = 10;
    private const MAX_RETRIES = 2;

    /**
     * Fetch a random product from FakeStore API
     *
     * @return array<string, mixed>|null Product data or null on failure
     * @throws ApiFetchException
     */
    public function fetchRandom(): ?array
    {
        $randomId = rand(1, self::MAX_ID);
        $url = self::BASE_URL . "/products/{$randomId}";

        $attempt = 0;
        $lastException = null;

        while ($attempt <= self::MAX_RETRIES) {
            try {
                $response = Http::timeout(self::TIMEOUT)->get($url);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::warning('FakeStore API request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'attempt' => $attempt + 1,
                ]);

                if ($attempt < self::MAX_RETRIES) {
                    $attempt++;
                    continue;
                }

                throw ApiFetchException::failed(
                    'FakeStore',
                    "HTTP {$response->status()}"
                );
            } catch (ApiFetchException $e) {
                throw $e;
            } catch (\Exception $e) {
                $lastException = $e;
                Log::error('FakeStore API exception', [
                    'url' => $url,
                    'message' => $e->getMessage(),
                    'attempt' => $attempt + 1,
                ]);

                if ($attempt < self::MAX_RETRIES) {
                    $attempt++;
                    // Wait before retry (exponential backoff)
                    usleep(100000 * ($attempt)); // 100ms, 200ms
                    continue;
                }
            }
        }

        throw ApiFetchException::failed(
            'FakeStore',
            $lastException?->getMessage() ?? 'Unknown error'
        );
    }
}

