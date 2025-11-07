<?php

namespace App\Contracts\Services;

interface ApiClientInterface
{
    /**
     * Fetch a random item from the API
     *
     * @return array<string, mixed>|null API response data or null on failure
     */
    public function fetchRandom(): ?array;
}

