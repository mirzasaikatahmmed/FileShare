<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Setting;

class FileUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // No auth required for public upload
    }

    public function rules(): array
    {
        // Get settings from database
        $maxFileSize = Setting::get('max_file_size', 100) * 1024; // Convert MB to KB
        $allowedExtensions = Setting::get('allowed_extensions', ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar']);
        $requirePassword = Setting::get('require_password', false);
        $blockExecutable = Setting::get('block_executable', true);
        $maxDownloadsLimit = Setting::get('max_downloads_limit', 100);

        // Filter out executable files if blocking is enabled
        if ($blockExecutable) {
            $executableExtensions = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'msi', 'dll', 'sh', 'app', 'deb', 'rpm', 'dmg', 'php', 'asp', 'aspx', 'jsp', 'py', 'rb', 'pl'];
            $allowedExtensions = array_diff($allowedExtensions, $executableExtensions);
        }

        $rules = [
            'file' => [
                'required',
                'file',
                'max:' . $maxFileSize,
                'mimes:' . implode(',', $allowedExtensions)
            ],
            'title' => 'required|string|max:255',
            'expires_at' => 'nullable|date|after:now',
            'max_downloads' => 'nullable|integer|min:1|max:' . $maxDownloadsLimit,
        ];

        // Make password required only if setting is enabled
        if ($requirePassword) {
            $rules['password'] = 'required|string|min:6|max:100';
        } else {
            $rules['password'] = 'nullable|string|min:6|max:100';
        }

        return $rules;
    }

    public function messages(): array
    {
        $maxFileSizeMB = Setting::get('max_file_size', 100);

        return [
            'file.required' => 'Please select a file to upload',
            'file.max' => 'File size must not exceed ' . $maxFileSizeMB . 'MB',
            'file.mimes' => 'File type not allowed. Please check allowed file formats in admin settings.',
            'title.required' => 'Please provide a title for your file',
            'password.required' => 'Password is required to protect your file',
            'password.min' => 'Password must be at least 6 characters',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->hasFile('file')) {
                $file = $this->file('file');
                $extension = strtolower($file->getClientOriginalExtension());

                // Check for dangerous file extensions
                $dangerousExtensions = ['exe', 'bat', 'cmd', 'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'msi', 'dll', 'sh', 'app', 'deb', 'rpm', 'dmg', 'php', 'asp', 'aspx', 'jsp', 'py', 'rb', 'pl'];

                if (in_array($extension, $dangerousExtensions) && Setting::get('block_executable', true)) {
                    $validator->errors()->add('file', 'Executable files are not allowed for security reasons.');
                }
            }
        });
    }
}
