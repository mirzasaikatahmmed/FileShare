<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    public function generate(string $url, string $filename): string
    {
        // Generate QR code as SVG (doesn't require imagick)
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->margin(2)
            ->generate($url);

        $path = 'qrcodes/' . $filename . '.svg';

        Storage::disk('public')->put($path, $qrCode);

        return $path;
    }

    public function regenerate(string $url, string $filename): string
    {
        // Delete old QR code if exists
        $oldPath = 'qrcodes/' . $filename . '.svg';
        if (Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        // Generate new one
        return $this->generate($url, $filename);
    }
}
