<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\ActivityLog;
use App\Helpers\IpHelper;
use App\Services\FileService;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function __construct(
        protected FileService $fileService
    ) {}

    public function access($shortCode)
    {
        $file = File::where('short_code', $shortCode)->firstOrFail();

        // Check if can download
        $check = $this->fileService->canDownload($file);
        if (!$check['success']) {
            abort(410, $check['message']);
        }

        return view('file.access', compact('file'));
    }

    public function verify(Request $request, $shortCode)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $file = File::where('short_code', $shortCode)->firstOrFail();

        // Verify password
        if (!$this->fileService->verifyPassword($file, $request->password)) {
            // Log failed attempt
            ActivityLog::create([
                'file_id' => $file->id,
                'action' => 'access_denied',
                'description' => 'Failed password attempt',
                'ip_address' => IpHelper::getClientIp(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->withErrors(['password' => 'Invalid password']);
        }

        // Store verification in session
        session(['verified_file_' . $file->id => true]);

        return redirect()->route('file.preview', $shortCode);
    }

    public function preview($shortCode)
    {
        $file = File::where('short_code', $shortCode)->firstOrFail();

        // Check if verified
        if (!session('verified_file_' . $file->id)) {
            return redirect()->route('file.access', $shortCode);
        }

        return view('file.preview', compact('file'));
    }

    public function download($shortCode)
    {
        $file = File::where('short_code', $shortCode)->firstOrFail();

        // Check if verified
        if (!session('verified_file_' . $file->id)) {
            abort(403, 'Unauthorized');
        }

        // Check if can download
        $check = $this->fileService->canDownload($file);
        if (!$check['success']) {
            abort(410, $check['message']);
        }

        return $this->fileService->download($file);
    }
}
