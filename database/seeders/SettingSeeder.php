<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Upload Settings
            ['key' => 'max_file_size', 'value' => '102400', 'type' => 'integer', 'description' => 'Maximum file size in KB (100MB)'],
            ['key' => 'allowed_extensions', 'value' => json_encode(['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'zip']), 'type' => 'json', 'description' => 'Allowed file extensions'],

            // Sharing Settings
            ['key' => 'default_expiration_days', 'value' => '7', 'type' => 'integer', 'description' => 'Default expiration days for files'],
            ['key' => 'max_expiration_days', 'value' => '30', 'type' => 'integer', 'description' => 'Maximum expiration days'],

            // Security Settings
            ['key' => 'rate_limit_uploads', 'value' => '10', 'type' => 'integer', 'description' => 'Max uploads per IP per hour'],
            ['key' => 'rate_limit_downloads', 'value' => '50', 'type' => 'integer', 'description' => 'Max downloads per IP per hour'],

            // System Settings
            ['key' => 'site_name', 'value' => 'File Share', 'type' => 'string', 'description' => 'Site name'],
            ['key' => 'auto_cleanup_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable automatic cleanup of expired files'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }

        $this->command->info('Settings seeded successfully!');
    }
}
