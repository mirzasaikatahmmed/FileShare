<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        // Get all settings grouped by category
        $settings = [
            'upload' => [
                'allowed_extensions' => Setting::get('allowed_extensions', ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar']),
                'max_file_size' => Setting::get('max_file_size', 100), // MB
                'max_uploads_per_day' => Setting::get('max_uploads_per_day', 50),
            ],
            'security' => [
                'require_password' => Setting::get('require_password', false),
                'scan_files' => Setting::get('scan_files', true),
                'block_executable' => Setting::get('block_executable', true),
            ],
            'storage' => [
                'default_expiry_days' => Setting::get('default_expiry_days', 7),
                'max_expiry_days' => Setting::get('max_expiry_days', 30),
                'auto_delete_after_expiry' => Setting::get('auto_delete_after_expiry', true),
            ],
            'download' => [
                'default_max_downloads' => Setting::get('default_max_downloads', 10),
                'max_downloads_limit' => Setting::get('max_downloads_limit', 100),
            ],
        ];

        // Get dangerous file extensions to display
        $dangerousExtensions = $this->getDangerousExtensions();
        $allExtensions = $this->getAllowedExtensionsList();

        return view('admin.settings.index', compact('settings', 'dangerousExtensions', 'allExtensions'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'allowed_extensions' => 'required|array|min:1',
            'allowed_extensions.*' => 'required|string|max:10',
            'max_file_size' => 'required|integer|min:1|max:1000',
            'max_uploads_per_day' => 'required|integer|min:1|max:1000',
            'require_password' => 'boolean',
            'scan_files' => 'boolean',
            'block_executable' => 'boolean',
            'default_expiry_days' => 'required|integer|min:1|max:365',
            'max_expiry_days' => 'required|integer|min:1|max:365',
            'auto_delete_after_expiry' => 'boolean',
            'default_max_downloads' => 'required|integer|min:1|max:1000',
            'max_downloads_limit' => 'required|integer|min:1|max:10000',
        ]);

        // Check for dangerous extensions
        $dangerousExtensions = $this->getDangerousExtensions();
        $selectedExtensions = $request->allowed_extensions;

        $hasDangerous = array_intersect($selectedExtensions, $dangerousExtensions);
        if (!empty($hasDangerous) && !$request->has('confirm_dangerous')) {
            return back()->withErrors([
                'allowed_extensions' => 'You are trying to allow dangerous file types: ' . implode(', ', $hasDangerous) . '. Please confirm this action.'
            ])->withInput();
        }

        // Upload Settings
        Setting::set('allowed_extensions', $selectedExtensions, 'json', 'Allowed file extensions for upload');
        Setting::set('max_file_size', $request->max_file_size, 'integer', 'Maximum file size in MB');
        Setting::set('max_uploads_per_day', $request->max_uploads_per_day, 'integer', 'Maximum uploads per IP per day');

        // Security Settings
        Setting::set('require_password', $request->has('require_password'), 'boolean', 'Require password for all uploads');
        Setting::set('scan_files', $request->has('scan_files'), 'boolean', 'Scan files for malware');
        Setting::set('block_executable', $request->has('block_executable'), 'boolean', 'Block executable files');

        // Storage Settings
        Setting::set('default_expiry_days', $request->default_expiry_days, 'integer', 'Default expiry days');
        Setting::set('max_expiry_days', $request->max_expiry_days, 'integer', 'Maximum expiry days');
        Setting::set('auto_delete_after_expiry', $request->has('auto_delete_after_expiry'), 'boolean', 'Auto delete files after expiry');

        // Download Settings
        Setting::set('default_max_downloads', $request->default_max_downloads, 'integer', 'Default maximum downloads');
        Setting::set('max_downloads_limit', $request->max_downloads_limit, 'integer', 'Maximum downloads limit');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully');
    }

    protected function getDangerousExtensions(): array
    {
        return [
            'exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js',
            'jar', 'msi', 'dll', 'sh', 'app', 'deb', 'rpm', 'dmg',
            'php', 'asp', 'aspx', 'jsp', 'py', 'rb', 'pl'
        ];
    }

    protected function getAllowedExtensionsList(): array
    {
        return [
            'Documents' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'odt', 'ods', 'odp'],
            'Images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico'],
            'Videos' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'],
            'Audio' => ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a'],
            'Archives' => ['zip', 'rar', '7z', 'tar', 'gz', 'bz2'],
            'Code' => ['html', 'css', 'js', 'json', 'xml', 'sql', 'csv'],
            'Others' => ['apk', 'ipa', 'epub', 'mobi'],
        ];
    }
}
