<?php

namespace App\Exceptions;

class ApiFetchException extends ImportException
{
    /**
     * Create exception for API fetch failure
     */
    public static function failed(string $apiName, ?string $reason = null): self
    {
        $message = "Failed to fetch data from {$apiName} API";

        if ($reason) {
            $message .= ": {$reason}";
        }

        return new self($message);
    }
}

