<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Download;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Overall statistics
        $stats = [
            'total_files' => File::count(),
            'total_downloads' => Download::count(),
            'total_storage_bytes' => File::sum('file_size'),
            'active_files' => File::where('is_active', true)->count(),
            'expired_files' => File::where('is_active', false)->count(),
            'avg_downloads_per_file' => round(Download::count() / max(File::count(), 1), 2),
            'total_storage' => $this->formatBytes(File::sum('file_size')),
        ];

        // Upload statistics (last 30 days)
        $uploadStats = File::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(file_size) as total_size')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Download statistics (last 30 days)
        $downloadStats = Download::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // File type distribution
        $fileTypes = File::select(
                'extension',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(file_size) as total_size')
            )
            ->groupBy('extension')
            ->orderBy('count', 'desc')
            ->get();

        // Top 10 downloaded files
        $topFiles = File::orderBy('downloads_count', 'desc')
            ->take(10)
            ->get();

        // Storage by month
        $storageByMonth = File::select(
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('SUM(file_size) as total_size'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('EXTRACT(YEAR FROM created_at)'), DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        // Recent activity
        $recentActivity = ActivityLog::with('file')
            ->latest()
            ->take(20)
            ->get();

        return view('admin.analytics.index', compact(
            'stats',
            'uploadStats',
            'downloadStats',
            'fileTypes',
            'topFiles',
            'storageByMonth',
            'recentActivity'
        ));
    }

    public function downloads(Request $request)
    {
        $query = Download::with('file');

        // Date range filter
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $query->whereBetween('created_at', [$dateFrom, $dateTo]);

        $downloads = $query->latest()->paginate(50);

        // Statistics for the period
        $stats = [
            'total' => $query->count(),
            'unique_ips' => $query->distinct('ip_address')->count('ip_address'),
            'unique_files' => $query->distinct('file_id')->count('file_id'),
        ];

        return view('admin.analytics.downloads', compact('downloads', 'stats', 'dateFrom', 'dateTo'));
    }

    public function uploads(Request $request)
    {
        $query = File::query();

        // Date range filter
        $dateFrom = $request->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $query->whereBetween('created_at', [$dateFrom, $dateTo]);

        $files = $query->latest()->paginate(50);

        // Statistics for the period
        $stats = [
            'total' => $query->count(),
            'total_size' => $this->formatBytes($query->sum('file_size')),
            'avg_size' => $this->formatBytes($query->avg('file_size')),
        ];

        return view('admin.analytics.uploads', compact('files', 'stats', 'dateFrom', 'dateTo'));
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
