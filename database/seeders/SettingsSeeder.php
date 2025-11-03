<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Upload Settings
            [
                'key' => 'allowed_extensions',
                'value' => json_encode(['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar', '7z']),
                'type' => 'json',
                'description' => 'Allowed file extensions for upload',
            ],
            [
                'key' => 'max_file_size',
                'value' => '100',
                'type' => 'integer',
                'description' => 'Maximum file size in MB',
            ],
            [
                'key' => 'max_uploads_per_day',
                'value' => '50',
                'type' => 'integer',
                'description' => 'Maximum uploads per IP per day',
            ],

            // Security Settings
            [
                'key' => 'require_password',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Require password for all uploads',
            ],
            [
                'key' => 'scan_files',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Scan files for malware',
            ],
            [
                'key' => 'block_executable',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Block executable files',
            ],

            // Storage Settings
            [
                'key' => 'default_expiry_days',
                'value' => '7',
                'type' => 'integer',
                'description' => 'Default expiry days',
            ],
            [
                'key' => 'max_expiry_days',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Maximum expiry days',
            ],
            [
                'key' => 'auto_delete_after_expiry',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Auto delete files after expiry',
            ],

            // Download Settings
            [
                'key' => 'default_max_downloads',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Default maximum downloads',
            ],
            [
                'key' => 'max_downloads_limit',
                'value' => '100',
                'type' => 'integer',
                'description' => 'Maximum downloads limit',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Settings seeded successfully!');
    }
}
