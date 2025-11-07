<?php

namespace App\Enums;

enum ImportSource: string
{
    case JSON_PLACEHOLDER = 'jsonplaceholder';
    case FAKE_STORE = 'fakestore';

    /**
     * Get all source values as array
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get source name for display
     */
    public function displayName(): string
    {
        return match ($this) {
            self::JSON_PLACEHOLDER => 'JSONPlaceholder',
            self::FAKE_STORE => 'FakeStore',
        };
    }
}

