<?php

namespace App\Services;

use App\Models\File;
use App\Models\ActivityLog;
use App\Helpers\IpHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class FileService
{
    public function upload($request, $shortCode = null)
    {
        $uploadedFile = $request->file('file');
        $title = $request->input('title');
        $password = $request->input('password');
        $expiresAt = $request->input('expires_at');
        $maxDownloads = $request->input('max_downloads');

        // Generate unique filename
        $originalName = $uploadedFile->getClientOriginalName();
        $extension = $uploadedFile->getClientOriginalExtension();
        $storedName = Str::uuid() . '.' . $extension;

        // Create storage path
        $path = sprintf(
            'files/%s/%s/%s',
            date('Y'),
            date('m'),
            $storedName
        );

        // Store file privately
        Storage::disk('private')->put($path, file_get_contents($uploadedFile));

        // Create database record
        $file = File::create([
            'title' => $title,
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'file_path' => $path,
            'file_size' => $uploadedFile->getSize(),
            'mime_type' => $uploadedFile->getMimeType(),
            'extension' => $extension,
            'password' => Hash::make($password),
            'short_code' => $shortCode ?: Str::random(8),
            'expires_at' => $expiresAt,
            'max_downloads' => $maxDownloads,
            'ip_address' => IpHelper::getClientIp(),
            'user_agent' => request()->userAgent(),
        ]);

        // Log activity
        ActivityLog::create([
            'file_id' => $file->id,
            'action' => 'upload',
            'description' => "File '{$title}' uploaded",
            'ip_address' => IpHelper::getClientIp(),
            'user_agent' => request()->userAgent(),
        ]);

        return $file;
    }

    public function download(File $file)
    {
        // Track download
        $file->increment('downloads_count');

        // Log download
        $file->downloads()->create([
            'ip_address' => IpHelper::getClientIp(),
            'user_agent' => request()->userAgent(),
            'downloaded_at' => now(),
        ]);

        // Log activity
        ActivityLog::create([
            'file_id' => $file->id,
            'action' => 'download',
            'description' => "File '{$file->title}' downloaded",
            'ip_address' => IpHelper::getClientIp(),
            'user_agent' => request()->userAgent(),
        ]);

        return Storage::disk('private')->download(
            $file->file_path,
            $file->original_name
        );
    }

    public function verifyPassword(File $file, string $password): bool
    {
        return Hash::check($password, $file->password);
    }

    public function canDownload(File $file): array
    {
        // Check if active
        if (!$file->is_active) {
            return ['success' => false, 'message' => 'This file is no longer available'];
        }

        // Check if expired
        if ($file->isExpired()) {
            return ['success' => false, 'message' => 'This file has expired'];
        }

        // Check download limit
        if ($file->hasReachedDownloadLimit()) {
            return ['success' => false, 'message' => 'Download limit reached'];
        }

        return ['success' => true];
    }

    public function delete(File $file, string $reason = null)
    {
        // Delete physical file
        if (Storage::disk('private')->exists($file->file_path)) {
            Storage::disk('private')->delete($file->file_path);
        }

        // Delete QR code
        if ($file->qr_code_path && Storage::disk('public')->exists($file->qr_code_path)) {
            Storage::disk('public')->delete($file->qr_code_path);
        }

        // Log activity
        ActivityLog::create([
            'file_id' => $file->id,
            'action' => 'delete',
            'description' => $reason ? "File deleted: {$reason}" : 'File deleted',
            'ip_address' => IpHelper::getClientIp(),
            'user_agent' => request()->userAgent(),
        ]);

        // Soft delete
        $file->delete();

        return true;
    }
}
