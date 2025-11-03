<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'ip_address',
        'user_agent',
        'country',
        'city',
        'downloaded_at',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the file that was downloaded.
     */
    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
