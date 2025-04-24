<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UploadFile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'upload_session_id',
        'original_filename',
        'stored_filename',
        'file_size',
        'mime_type',
        'download_count',
    ];

    /**
     * Get the upload session that this file belongs to.
     */
    public function uploadSession(): BelongsTo
    {
        return $this->belongsTo(UploadSession::class);
    }

    /**
     * Increment the download count.
     *
     * @return void
     */
    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    /**
     * Get the file's storage path.
     *
     * @return string
     */
    public function getStoragePath(): string
    {
        return 'uploads/' . $this->stored_filename;
    }

    /**
     * Get the file's download URL.
     *
     * @return string
     */
    public function getDownloadUrl(): string
    {
        return url('downloads/' . $this->stored_filename);
    }
}
