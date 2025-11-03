<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@fileshare.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@fileshare.com');
        $this->command->info('Password: password');
    }
}
