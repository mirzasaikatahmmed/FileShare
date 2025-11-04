<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Download;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistics
        $stats = [
            'total_files' => File::count(),
            'total_downloads' => Download::count(),
            'total_storage' => File::sum('file_size'),
            'active_files' => File::where('is_active', true)->count(),
            'today_uploads' => File::whereDate('created_at', today())->count(),
            'today_downloads' => Download::whereDate('created_at', today())->count(),
        ];

        // Recent uploads (last 10)
        $recent_uploads = File::latest()
            ->take(10)
            ->get();

        // Upload trend (last 30 days)
        $upload_trend = File::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Download trend (last 30 days)
        $download_trend = Download::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Top files by downloads
        $top_files = File::orderBy('downloads_count', 'desc')
            ->take(10)
            ->get();

        // File type distribution
        $file_types = File::select('extension', DB::raw('COUNT(*) as count'))
            ->groupBy('extension')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_uploads',
            'upload_trend',
            'download_trend',
            'top_files',
            'file_types'
        ));
    }

    public function logs(Request $request)
    {
        $query = ActivityLog::with('file')->latest();

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by IP
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        $logs = $query->paginate(50);

        return view('admin.logs', compact('logs'));
    }
}
