<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UploadSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'token',
        'expires_at',
        'password',
        'email_to_notify',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the files associated with this upload session.
     */
    public function files(): HasMany
    {
        return $this->hasMany(UploadFile::class);
    }

    /**
     * Check if the upload session has expired.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Calculate total size of all files in the session
     *
     * @return int
     */
    public function totalSize(): int
    {
        return $this->files->sum('file_size');
    }
}
