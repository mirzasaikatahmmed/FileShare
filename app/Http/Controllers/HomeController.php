<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Services\FileService;
use App\Services\QrCodeService;
use App\Services\ShortUrlService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected FileService $fileService,
        protected QrCodeService $qrCodeService,
        protected ShortUrlService $shortUrlService
    ) {}

    public function index()
    {
        return view('welcome');
    }

    public function upload(FileUploadRequest $request)
    {
        try {
            // Generate a temporary short code (random string)
            $tempShortCode = \Illuminate\Support\Str::random(8);

            // Upload file with temporary short code
            $file = $this->fileService->upload($request, $tempShortCode);

            // Generate proper short URL code based on file ID
            $shortCode = $this->shortUrlService->generate($file->id);
            $file->update(['short_code' => $shortCode]);

            // Generate QR code
            $qrCodePath = $this->qrCodeService->generate(
                route('file.access', $shortCode),
                $shortCode
            );
            $file->update(['qr_code_path' => $qrCodePath]);

            return view('file.success', [
                'file' => $file->fresh(),
                'shortUrl' => route('file.access', $shortCode),
                'qrCodeUrl' => asset('storage/' . $qrCodePath)
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
