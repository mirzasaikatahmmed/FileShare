<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\Request;

class AdminFileController extends Controller
{
    public function __construct(
        protected FileService $fileService
    ) {}

    public function index(Request $request)
    {
        $query = File::withCount('downloads')->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%")
                  ->orWhere('short_code', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'expired') {
                $query->where(function($q) {
                    $q->where('is_active', false)
                      ->orWhere(function($q2) {
                          $q2->whereNotNull('expires_at')
                             ->where('expires_at', '<', now());
                      });
                });
            }
        }

        // Filter by file type
        if ($request->filled('type')) {
            $query->where('extension', $request->type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'downloads':
                    $query->orderBy('downloads_count', 'desc');
                    break;
                case 'size':
                    $query->orderBy('file_size', 'desc');
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                default:
                    $query->latest();
            }
        }

        $files = $query->paginate(20);

        // Get file types for filter
        $fileTypes = File::select('extension')
            ->distinct()
            ->pluck('extension');

        return view('admin.files.index', compact('files', 'fileTypes'));
    }

    public function show(File $file)
    {
        $file->load(['downloads' => function($query) {
            $query->latest()->take(50);
        }, 'activityLogs' => function($query) {
            $query->latest()->take(20);
        }]);

        return view('admin.files.show', compact('file'));
    }

    public function destroy(File $file)
    {
        $this->fileService->delete($file, 'Deleted by admin');

        return redirect()->route('admin.files.index')
            ->with('success', 'File deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'file_ids' => 'required|array',
            'file_ids.*' => 'exists:files,id'
        ]);

        $files = File::whereIn('id', $request->file_ids)->get();

        foreach ($files as $file) {
            $this->fileService->delete($file, 'Bulk deleted by admin');
        }

        return redirect()->route('admin.files.index')
            ->with('success', count($files) . ' files deleted successfully');
    }
}
