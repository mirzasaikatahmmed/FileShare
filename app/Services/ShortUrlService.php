<?php

namespace App\Services;

use Hashids\Hashids;
use Illuminate\Support\Str;

class ShortUrlService
{
    protected $hashids;

    public function __construct()
    {
        $this->hashids = new Hashids(
            config('hashids.connections.main.salt'),
            config('hashids.connections.main.length'),
            config('hashids.connections.main.alphabet')
        );
    }

    /**
     * Generate short code for file ID.
     * Using Hashids for reversible encoding.
     */
    public function generate(int $fileId): string
    {
        return $this->hashids->encode($fileId);
    }

    /**
     * Decode short code to get file ID.
     */
    public function decode(string $shortCode): ?int
    {
        $decoded = $this->hashids->decode($shortCode);
        return $decoded[0] ?? null;
    }

    /**
     * Generate random short code (alternative method - more secure but not reversible).
     */
    public function generateRandom(int $length = 8): string
    {
        return Str::random($length);
    }
}
